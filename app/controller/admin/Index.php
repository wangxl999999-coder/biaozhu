<?php
namespace app\controller\admin;

use app\BaseController;
use app\model\Admin;
use app\model\User;
use app\model\Task;
use app\model\UserTask;
use app\model\Earnings;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;

class Index extends BaseController
{
    protected $middleware = [
        \app\middleware\AdminAuth::class => ['except' => ['login', 'logout']]
    ];

    public function index()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $userCount = User::count();
        $taskCount = Task::count();
        $activeTaskCount = Task::where('status', 1)->count();
        $earningsTotal = Earnings::where('status', 1)->sum('amount');

        $todayStart = strtotime(date('Y-m-d 00:00:00'));
        $todayEnd = strtotime(date('Y-m-d 23:59:59'));
        
        $todayUserCount = User::whereBetween('create_time', [$todayStart, $todayEnd])->count();
        $todayTaskCount = Task::whereBetween('create_time', [$todayStart, $todayEnd])->count();
        $todayEarnings = Earnings::where('status', 1)->whereBetween('create_time', [$todayStart, $todayEnd])->sum('amount');

        $recentTasks = Task::with(['category'])
            ->order('create_time', 'desc')
            ->limit(5)
            ->select();

        $recentUsers = User::order('create_time', 'desc')
            ->limit(5)
            ->select();

        View::assign('userCount', $userCount);
        View::assign('taskCount', $taskCount);
        View::assign('activeTaskCount', $activeTaskCount);
        View::assign('earningsTotal', $earningsTotal);
        View::assign('todayUserCount', $todayUserCount);
        View::assign('todayTaskCount', $todayTaskCount);
        View::assign('todayEarnings', $todayEarnings);
        View::assign('recentTasks', $recentTasks);
        View::assign('recentUsers', $recentUsers);

        return View::fetch();
    }

    public function login()
    {
        if (Session::has('admin_id')) {
            return redirect('/admin');
        }

        if (Request::isPost()) {
            $username = Request::post('username');
            $password = Request::post('password');

            if (empty($username) || empty($password)) {
                return json(['code' => 0, 'msg' => '用户名和密码不能为空']);
            }

            $admin = Admin::where('username', $username)->find();
            if (!$admin) {
                return json(['code' => 0, 'msg' => '用户名或密码错误']);
            }

            if (!password_verify($password, $admin->password)) {
                return json(['code' => 0, 'msg' => '用户名或密码错误']);
            }

            if ($admin->status != 1) {
                return json(['code' => 0, 'msg' => '账号已被禁用']);
            }

            $admin->last_login_time = time();
            $admin->last_login_ip = Request::ip();
            $admin->save();

            Session::set('admin_id', $admin->id);
            Session::set('admin_username', $admin->username);
            Session::set('admin_nickname', $admin->nickname);

            return json(['code' => 1, 'msg' => '登录成功']);
        }

        return View::fetch();
    }

    public function logout()
    {
        Session::clear();
        return redirect('/admin/login');
    }
}
