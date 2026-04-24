<?php
namespace app\controller\admin;

use app\BaseController;
use app\model\Admin;
use app\model\User as UserModel;
use app\model\UserEducation;
use app\model\UserWork;
use app\model\UserTask;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;

class User extends BaseController
{
    protected $middleware = [\app\middleware\AdminAuth::class];

    public function index()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $keyword = Request::get('keyword', '');
        $status = Request::get('status', -1);

        $query = UserModel::with(['education', 'work']);

        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('username', 'like', '%' . $keyword . '%')
                  ->whereOr('nickname', 'like', '%' . $keyword . '%')
                  ->whereOr('phone', 'like', '%' . $keyword . '%')
                  ->whereOr('email', 'like', '%' . $keyword . '%');
            });
        }

        if ($status >= 0) {
            $query->where('status', $status);
        }

        $users = $query->order('create_time', 'desc')
            ->paginate(15);

        View::assign('users', $users);
        View::assign('keyword', $keyword);
        View::assign('status', $status);

        return View::fetch();
    }

    public function detail()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $id = Request::get('id');
        if (!$id) {
            return redirect('/admin/user');
        }

        $user = UserModel::with(['education', 'work', 'tasks.task', 'earnings'])
            ->find($id);

        if (!$user) {
            return redirect('/admin/user');
        }

        View::assign('user', $user);

        return View::fetch();
    }

    public function updateStatus()
    {
        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $id = Request::post('id');
        $status = Request::post('status');

        $user = UserModel::find($id);
        if (!$user) {
            return json(['code' => 0, 'msg' => '用户不存在']);
        }

        $user->status = $status;
        $user->update_time = time();

        if ($user->save()) {
            return json(['code' => 1, 'msg' => '状态更新成功']);
        }

        return json(['code' => 0, 'msg' => '状态更新失败']);
    }

    public function resetPassword()
    {
        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $id = Request::post('id');
        $password = Request::post('password');

        if (strlen($password) < 6) {
            return json(['code' => 0, 'msg' => '密码长度不能少于6位']);
        }

        $user = UserModel::find($id);
        if (!$user) {
            return json(['code' => 0, 'msg' => '用户不存在']);
        }

        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->update_time = time();

        if ($user->save()) {
            return json(['code' => 1, 'msg' => '密码重置成功']);
        }

        return json(['code' => 0, 'msg' => '密码重置失败']);
    }
}
