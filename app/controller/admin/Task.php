<?php
namespace app\controller\admin;

use app\BaseController;
use app\model\Admin;
use app\model\Task as TaskModel;
use app\model\TaskCategory;
use app\model\UserTask;
use app\model\User;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;

class Task extends BaseController
{
    protected $middleware = [\app\middleware\AdminAuth::class];

    public function index()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $keyword = Request::get('keyword', '');
        $categoryId = Request::get('category_id', 0);
        $status = Request::get('status', -1);

        $query = TaskModel::with(['category']);

        if ($keyword) {
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

        if ($status >= 0) {
            $query->where('status', $status);
        }

        $tasks = $query->order('create_time', 'desc')
            ->paginate(15);

        $categories = TaskCategory::where('status', 1)
            ->order('sort', 'asc')
            ->select();

        View::assign('tasks', $tasks);
        View::assign('categories', $categories);
        View::assign('keyword', $keyword);
        View::assign('categoryId', $categoryId);
        View::assign('status', $status);

        return View::fetch();
    }

    public function add()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $categories = TaskCategory::where('status', 1)
            ->order('sort', 'asc')
            ->select();

        View::assign('categories', $categories);

        if (Request::isPost()) {
            $task = new TaskModel();
            $task->title = Request::post('title');
            $task->category_id = Request::post('category_id');
            $task->price = Request::post('price');
            $task->description = Request::post('description');
            $task->requirements = Request::post('requirements');
            $task->process = Request::post('process');
            $task->dingtalk_qrcode = Request::post('dingtalk_qrcode');
            $task->tags = Request::post('tags');
            $task->total_count = Request::post('total_count');
            $task->applied_count = 0;
            $task->view_count = 0;
            $task->status = Request::post('status', 1);
            $task->start_time = strtotime(Request::post('start_time')) ?: time();
            $task->end_time = Request::post('end_time') ? strtotime(Request::post('end_time')) : 0;
            $task->create_time = time();
            $task->update_time = time();

            if ($task->save()) {
                return json(['code' => 1, 'msg' => '任务创建成功']);
            }

            return json(['code' => 0, 'msg' => '任务创建失败']);
        }

        return View::fetch();
    }

    public function edit()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $id = Request::get('id');
        if (!$id) {
            return redirect('/admin/task');
        }

        $task = TaskModel::find($id);
        if (!$task) {
            return redirect('/admin/task');
        }

        $categories = TaskCategory::where('status', 1)
            ->order('sort', 'asc')
            ->select();

        View::assign('task', $task);
        View::assign('categories', $categories);

        if (Request::isPost()) {
            $task->title = Request::post('title');
            $task->category_id = Request::post('category_id');
            $task->price = Request::post('price');
            $task->description = Request::post('description');
            $task->requirements = Request::post('requirements');
            $task->process = Request::post('process');
            $task->dingtalk_qrcode = Request::post('dingtalk_qrcode');
            $task->tags = Request::post('tags');
            $task->total_count = Request::post('total_count');
            $task->status = Request::post('status', 1);
            $task->start_time = strtotime(Request::post('start_time')) ?: $task->start_time;
            $task->end_time = Request::post('end_time') ? strtotime(Request::post('end_time')) : $task->end_time;
            $task->update_time = time();

            if ($task->save()) {
                return json(['code' => 1, 'msg' => '任务更新成功']);
            }

            return json(['code' => 0, 'msg' => '任务更新失败']);
        }

        return View::fetch();
    }

    public function detail()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $id = Request::get('id');
        if (!$id) {
            return redirect('/admin/task');
        }

        $task = TaskModel::with(['category'])
            ->find($id);

        if (!$task) {
            return redirect('/admin/task');
        }

        $userTasks = UserTask::with(['user'])
            ->where('task_id', $id)
            ->order('create_time', 'desc')
            ->select();

        View::assign('task', $task);
        View::assign('userTasks', $userTasks);

        return View::fetch();
    }

    public function audit()
    {
        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $id = Request::post('id');
        $status = Request::post('status');
        $remark = Request::post('remark', '');
        $earnings = Request::post('earnings', 0);

        $userTask = UserTask::with(['task', 'user'])->find($id);
        if (!$userTask) {
            return json(['code' => 0, 'msg' => '记录不存在']);
        }

        if ($userTask->status != 2) {
            return json(['code' => 0, 'msg' => '只有待审核状态可以操作']);
        }

        $userTask->status = $status;
        $userTask->audit_time = time();
        $userTask->audit_remark = $remark;
        $userTask->update_time = time();

        if ($status == 3 && $earnings > 0) {
            $userTask->earnings = $earnings;
            
            $user = User::find($userTask->user_id);
            if ($user) {
                $user->pending_balance = $user->pending_balance + $earnings;
                $user->save();
            }
        }

        if ($userTask->save()) {
            return json(['code' => 1, 'msg' => '审核成功']);
        }

        return json(['code' => 0, 'msg' => '审核失败']);
    }

    public function categories()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $categories = TaskCategory::order('sort', 'asc')
            ->select();

        View::assign('categories', $categories);

        return View::fetch();
    }

    public function saveCategory()
    {
        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $id = Request::post('id', 0);
        $name = Request::post('name');
        $sort = Request::post('sort', 0);
        $status = Request::post('status', 1);

        if (empty($name)) {
            return json(['code' => 0, 'msg' => '分类名称不能为空']);
        }

        if ($id > 0) {
            $category = TaskCategory::find($id);
            if (!$category) {
                return json(['code' => 0, 'msg' => '分类不存在']);
            }
        } else {
            $category = new TaskCategory();
            $category->create_time = time();
        }

        $category->name = $name;
        $category->sort = $sort;
        $category->status = $status;
        $category->update_time = time();

        if ($category->save()) {
            return json(['code' => 1, 'msg' => $id > 0 ? '更新成功' : '创建成功']);
        }

        return json(['code' => 0, 'msg' => '操作失败']);
    }
}
