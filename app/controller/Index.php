<?php
namespace app\controller;

use app\BaseController;
use app\model\User;
use app\model\Task;
use app\model\Faq;
use app\model\FaqCategory;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;

class Index extends BaseController
{
    public function index()
    {
        $userId = Session::get('user_id');
        $user = null;
        
        if ($userId) {
            $user = User::find($userId);
            View::assign('user', $user);
        }

        $hotTasks = Task::where('status', 1)
            ->order('view_count', 'desc')
            ->limit(6)
            ->select();

        $newTasks = Task::where('status', 1)
            ->order('create_time', 'desc')
            ->limit(6)
            ->select();

        View::assign('hotTasks', $hotTasks);
        View::assign('newTasks', $newTasks);
        
        return View::fetch();
    }

    public function register()
    {
        if (Session::get('user_id')) {
            return redirect('/');
        }

        if (Request::isPost()) {
            $username = Request::post('username');
            $password = Request::post('password');
            $confirmPassword = Request::post('confirm_password');
            $nickname = Request::post('nickname');

            if (empty($username) || empty($password)) {
                return json(['code' => 0, 'msg' => '用户名和密码不能为空']);
            }

            if ($password !== $confirmPassword) {
                return json(['code' => 0, 'msg' => '两次密码不一致']);
            }

            if (strlen($password) < 6) {
                return json(['code' => 0, 'msg' => '密码长度不能少于6位']);
            }

            $exists = User::where('username', $username)->find();
            if ($exists) {
                return json(['code' => 0, 'msg' => '用户名已存在']);
            }

            $user = new User();
            $user->username = $username;
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->nickname = $nickname ?: $username;
            $user->create_time = time();
            $user->update_time = time();

            if ($user->save()) {
                Session::set('user_id', $user->id);
                Session::set('username', $user->username);
                Session::set('nickname', $user->nickname);
                
                return json(['code' => 1, 'msg' => '注册成功', 'data' => ['redirect' => '/user/profile']]);
            }

            return json(['code' => 0, 'msg' => '注册失败，请重试']);
        }

        return View::fetch();
    }

    public function login()
    {
        if (Session::get('user_id')) {
            return redirect('/');
        }

        if (Request::isPost()) {
            $username = Request::post('username');
            $password = Request::post('password');

            if (empty($username) || empty($password)) {
                return json(['code' => 0, 'msg' => '用户名和密码不能为空']);
            }

            $user = User::where('username', $username)->find();
            if (!$user) {
                return json(['code' => 0, 'msg' => '用户名或密码错误']);
            }

            if (!password_verify($password, $user->password)) {
                return json(['code' => 0, 'msg' => '用户名或密码错误']);
            }

            if ($user->status != 1) {
                return json(['code' => 0, 'msg' => '账号已被禁用，请联系管理员']);
            }

            Session::set('user_id', $user->id);
            Session::set('username', $user->username);
            Session::set('nickname', $user->nickname);

            $redirect = '/';
            if ($user->education_status == 0 || $user->work_status == 0) {
                $redirect = '/user/profile';
            }

            return json(['code' => 1, 'msg' => '登录成功', 'data' => ['redirect' => $redirect]]);
        }

        return View::fetch();
    }

    public function logout()
    {
        Session::clear();
        return redirect('/login');
    }

    public function faq()
    {
        $userId = Session::get('user_id');
        $user = null;
        
        if ($userId) {
            $user = User::find($userId);
            View::assign('user', $user);
        }

        $categories = FaqCategory::where('status', 1)
            ->order('sort', 'asc')
            ->select();

        $categoryId = Request::get('category_id', 0);
        
        $faqQuery = Faq::where('status', 1);
        if ($categoryId > 0) {
            $faqQuery->where('category_id', $categoryId);
        }
        
        $faqs = $faqQuery->order('sort', 'asc')
            ->order('create_time', 'desc')
            ->select();

        View::assign('categories', $categories);
        View::assign('faqs', $faqs);
        View::assign('currentCategory', $categoryId);

        return View::fetch();
    }
}
