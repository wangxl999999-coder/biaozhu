<?php
namespace app\controller;

use app\BaseController;
use app\model\User;
use app\model\UserEducation;
use app\model\UserWork;
use app\model\UserTask;
use app\model\Earnings;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;

class User extends BaseController
{
    protected $middleware = [\app\middleware\Auth::class];

    public function profile()
    {
        $userId = Session::get('user_id');
        $user = User::with(['education', 'work'])->find($userId);
        
        View::assign('user', $user);
        
        $showTip = ($user->education_status == 0 || $user->work_status == 0);
        View::assign('showTip', $showTip);

        return View::fetch();
    }

    public function saveEducation()
    {
        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $userId = Session::get('user_id');
        $user = User::find($userId);

        $school = Request::post('school');
        $major = Request::post('major');
        $degree = Request::post('degree');
        $startYear = Request::post('start_year');
        $endYear = Request::post('end_year');

        if (empty($school) || empty($major) || empty($degree)) {
            return json(['code' => 0, 'msg' => '请填写完整的教育经历']);
        }

        $existing = UserEducation::where('user_id', $userId)->find();
        
        if ($existing) {
            $education = $existing;
        } else {
            $education = new UserEducation();
            $education->user_id = $userId;
        }

        $education->school = $school;
        $education->major = $major;
        $education->degree = $degree;
        $education->start_year = $startYear;
        $education->end_year = $endYear;
        $education->create_time = $education->create_time ?: time();
        $education->update_time = time();

        if ($education->save()) {
            $user->education_status = 1;
            $user->save();
            
            return json(['code' => 1, 'msg' => '教育经历保存成功']);
        }

        return json(['code' => 0, 'msg' => '保存失败，请重试']);
    }

    public function saveWork()
    {
        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $userId = Session::get('user_id');
        $user = User::find($userId);

        $company = Request::post('company');
        $position = Request::post('position');
        $description = Request::post('description');
        $startDate = Request::post('start_date');
        $endDate = Request::post('end_date');
        $isCurrent = Request::post('is_current', 0);

        if (empty($company) || empty($position)) {
            return json(['code' => 0, 'msg' => '请填写公司名称和职位']);
        }

        $existing = UserWork::where('user_id', $userId)->find();
        
        if ($existing) {
            $work = $existing;
        } else {
            $work = new UserWork();
            $work->user_id = $userId;
        }

        $work->company = $company;
        $work->position = $position;
        $work->description = $description;
        $work->start_date = $startDate;
        $work->end_date = $isCurrent ? null : $endDate;
        $work->is_current = $isCurrent;
        $work->create_time = $work->create_time ?: time();
        $work->update_time = time();

        if ($work->save()) {
            $user->work_status = 1;
            $user->save();
            
            return json(['code' => 1, 'msg' => '工作经历保存成功']);
        }

        return json(['code' => 0, 'msg' => '保存失败，请重试']);
    }

    public function center()
    {
        $userId = Session::get('user_id');
        $user = User::find($userId);
        
        $pendingCount = UserTask::where('user_id', $userId)
            ->where('status', 2)
            ->count();

        $passedCount = UserTask::where('user_id', $userId)
            ->where('status', 3)
            ->count();

        $rejectedCount = UserTask::where('user_id', $userId)
            ->where('status', 4)
            ->count();

        View::assign('user', $user);
        View::assign('pendingCount', $pendingCount);
        View::assign('passedCount', $passedCount);
        View::assign('rejectedCount', $rejectedCount);

        return View::fetch();
    }

    public function earnings()
    {
        $userId = Session::get('user_id');
        $user = User::find($userId);

        $type = Request::get('type', 'all');

        $query = Earnings::where('user_id', $userId);
        
        if ($type == 'pending') {
            $query->where('status', 0);
        } elseif ($type == 'settled') {
            $query->where('status', 1);
        }

        $earnings = $query->order('create_time', 'desc')
            ->paginate(10);

        View::assign('user', $user);
        View::assign('earnings', $earnings);
        View::assign('type', $type);

        return View::fetch();
    }

    public function myTasks()
    {
        $userId = Session::get('user_id');
        $user = User::find($userId);

        $status = Request::get('status', 'all');

        $query = UserTask::with(['task'])
            ->where('user_id', $userId);

        $statusMap = [
            'pending_submit' => 1,
            'pending_audit' => 2,
            'passed' => 3,
            'rejected' => 4,
        ];

        if ($status != 'all' && isset($statusMap[$status])) {
            $query->where('status', $statusMap[$status]);
        }

        $userTasks = $query->order('create_time', 'desc')
            ->paginate(10);

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
        View::assign('userTasks', $userTasks);
        View::assign('currentStatus', $status);
        View::assign('statusText', $statusText);
        View::assign('statusClass', $statusClass);

        return View::fetch();
    }
}
