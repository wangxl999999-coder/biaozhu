<?php
namespace app\controller;

use app\BaseController;
use app\model\User;
use app\model\Task as TaskModel;
use app\model\TaskCategory;
use app\model\UserTask;
use app\model\TaskComment;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;

class Task extends BaseController
{
    public function index()
    {
        $userId = Session::get('user_id');
        $user = null;
        
        if ($userId) {
            $user = User::find($userId);
            View::assign('user', $user);
        }

        $categories = TaskCategory::where('status', 1)
            ->order('sort', 'asc')
            ->select();

        $keyword = Request::get('keyword', '');
        $categoryId = Request::get('category_id', 0);
        $sort = Request::get('sort', 'new');

        $query = TaskModel::where('status', 1);

        if ($keyword) {
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

        switch ($sort) {
            case 'hot':
                $query->order('view_count', 'desc');
                break;
            case 'price':
                $query->order('price', 'desc');
                break;
            case 'new':
            default:
                $query->order('create_time', 'desc');
                break;
        }

        $tasks = $query->paginate(12);

        $sortText = [
            'new' => '最新发布',
            'hot' => '热度最高',
            'price' => '价格最高',
        ];

        View::assign('categories', $categories);
        View::assign('tasks', $tasks);
        View::assign('keyword', $keyword);
        View::assign('categoryId', $categoryId);
        View::assign('sort', $sort);
        View::assign('sortText', $sortText);

        return View::fetch();
    }

    public function detail()
    {
        $id = Request::get('id');
        if (!$id) {
            return redirect('/task');
        }

        $task = TaskModel::with(['category'])
            ->where('id', $id)
            ->find();

        if (!$task) {
            return redirect('/task');
        }

        $task->view_count = $task->view_count + 1;
        $task->save();

        $userId = Session::get('user_id');
        $user = null;
        $hasApplied = false;
        $userTask = null;

        if ($userId) {
            $user = User::find($userId);
            $userTask = UserTask::where('user_id', $userId)
                ->where('task_id', $id)
                ->find();
            $hasApplied = !empty($userTask);
        }

        $comments = TaskComment::with(['user'])
            ->where('task_id', $id)
            ->where('status', 1)
            ->where('parent_id', 0)
            ->order('create_time', 'desc')
            ->select();

        $statusText = [
            1 => '待提交',
            2 => '待审核',
            3 => '通过审核',
            4 => '已拒绝',
        ];

        $statusClass = [
            1 => 'primary',
            2 => 'warning',
            3 => 'success',
            4 => 'danger',
        ];

        View::assign('user', $user);
        View::assign('task', $task);
        View::assign('hasApplied', $hasApplied);
        View::assign('userTask', $userTask);
        View::assign('comments', $comments);
        View::assign('statusText', $statusText);
        View::assign('statusClass', $statusClass);

        return View::fetch();
    }

    public function apply()
    {
        if (!Session::has('user_id')) {
            return json(['code' => 0, 'msg' => '请先登录', 'data' => ['redirect' => '/login']]);
        }

        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $userId = Session::get('user_id');
        $taskId = Request::post('task_id');

        $task = TaskModel::find($taskId);
        if (!$task) {
            return json(['code' => 0, 'msg' => '任务不存在']);
        }

        if ($task->status != 1) {
            return json(['code' => 0, 'msg' => '该任务已结束']);
        }

        $userTask = UserTask::where('user_id', $userId)
            ->where('task_id', $taskId)
            ->find();

        if ($userTask) {
            return json(['code' => 0, 'msg' => '您已申请过该任务']);
        }

        if ($task->applied_count >= $task->total_count && $task->total_count > 0) {
            return json(['code' => 0, 'msg' => '该任务名额已满']);
        }

        $userTask = new UserTask();
        $userTask->user_id = $userId;
        $userTask->task_id = $taskId;
        $userTask->status = 1;
        $userTask->create_time = time();
        $userTask->update_time = time();

        if ($userTask->save()) {
            $task->applied_count = $task->applied_count + 1;
            $task->save();

            return json([
                'code' => 1, 
                'msg' => '申请成功', 
                'data' => [
                    'qrcode' => $task->dingtalk_qrcode,
                    'task_title' => $task->title
                ]
            ]);
        }

        return json(['code' => 0, 'msg' => '申请失败，请重试']);
    }

    public function submit()
    {
        if (!Session::has('user_id')) {
            return json(['code' => 0, 'msg' => '请先登录', 'data' => ['redirect' => '/login']]);
        }

        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $userId = Session::get('user_id');
        $taskId = Request::post('task_id');

        $userTask = UserTask::where('user_id', $userId)
            ->where('task_id', $taskId)
            ->find();

        if (!$userTask) {
            return json(['code' => 0, 'msg' => '您未申请该任务']);
        }

        if ($userTask->status != 1) {
            return json(['code' => 0, 'msg' => '该任务状态不允许提交']);
        }

        $userTask->status = 2;
        $userTask->submit_time = time();
        $userTask->update_time = time();

        if ($userTask->save()) {
            return json(['code' => 1, 'msg' => '提交成功，等待审核']);
        }

        return json(['code' => 0, 'msg' => '提交失败，请重试']);
    }

    public function comment()
    {
        if (!Session::has('user_id')) {
            return json(['code' => 0, 'msg' => '请先登录', 'data' => ['redirect' => '/login']]);
        }

        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $userId = Session::get('user_id');
        $taskId = Request::post('task_id');
        $content = Request::post('content');

        if (empty(trim($content))) {
            return json(['code' => 0, 'msg' => '评论内容不能为空']);
        }

        $comment = new TaskComment();
        $comment->task_id = $taskId;
        $comment->user_id = $userId;
        $comment->content = $content;
        $comment->status = 1;
        $comment->create_time = time();
        $comment->update_time = time();

        if ($comment->save()) {
            return json(['code' => 1, 'msg' => '评论成功']);
        }

        return json(['code' => 0, 'msg' => '评论失败，请重试']);
    }
}
