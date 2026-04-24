<?php
namespace app\controller\admin;

use app\BaseController;
use app\model\Admin;
use app\model\Faq as FaqModel;
use app\model\FaqCategory;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;

class Faq extends BaseController
{
    protected $middleware = [\app\middleware\AdminAuth::class];

    public function index()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $categoryId = Request::get('category_id', 0);
        $keyword = Request::get('keyword', '');

        $query = FaqModel::with(['category']);

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

        if ($keyword) {
            $query->where('question', 'like', '%' . $keyword . '%');
        }

        $faqs = $query->order('sort', 'asc')
            ->order('create_time', 'desc')
            ->paginate(15);

        $categories = FaqCategory::where('status', 1)
            ->order('sort', 'asc')
            ->select();

        View::assign('faqs', $faqs);
        View::assign('categories', $categories);
        View::assign('categoryId', $categoryId);
        View::assign('keyword', $keyword);

        return View::fetch();
    }

    public function add()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $categories = FaqCategory::where('status', 1)
            ->order('sort', 'asc')
            ->select();

        View::assign('categories', $categories);

        if (Request::isPost()) {
            $faq = new FaqModel();
            $faq->category_id = Request::post('category_id', 0);
            $faq->question = Request::post('question');
            $faq->answer = Request::post('answer');
            $faq->view_count = 0;
            $faq->sort = Request::post('sort', 0);
            $faq->status = Request::post('status', 1);
            $faq->create_time = time();
            $faq->update_time = time();

            if ($faq->save()) {
                return json(['code' => 1, 'msg' => 'FAQ创建成功']);
            }

            return json(['code' => 0, 'msg' => 'FAQ创建失败']);
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
            return redirect('/admin/faq');
        }

        $faq = FaqModel::find($id);
        if (!$faq) {
            return redirect('/admin/faq');
        }

        $categories = FaqCategory::where('status', 1)
            ->order('sort', 'asc')
            ->select();

        View::assign('faq', $faq);
        View::assign('categories', $categories);

        if (Request::isPost()) {
            $faq->category_id = Request::post('category_id', 0);
            $faq->question = Request::post('question');
            $faq->answer = Request::post('answer');
            $faq->sort = Request::post('sort', 0);
            $faq->status = Request::post('status', 1);
            $faq->update_time = time();

            if ($faq->save()) {
                return json(['code' => 1, 'msg' => 'FAQ更新成功']);
            }

            return json(['code' => 0, 'msg' => 'FAQ更新失败']);
        }

        return View::fetch();
    }

    public function categories()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $categories = FaqCategory::order('sort', 'asc')
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
            $category = FaqCategory::find($id);
            if (!$category) {
                return json(['code' => 0, 'msg' => '分类不存在']);
            }
        } else {
            $category = new FaqCategory();
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

    public function delete()
    {
        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $id = Request::post('id');

        $faq = FaqModel::find($id);
        if (!$faq) {
            return json(['code' => 0, 'msg' => 'FAQ不存在']);
        }

        if ($faq->delete()) {
            return json(['code' => 1, 'msg' => '删除成功']);
        }

        return json(['code' => 0, 'msg' => '删除失败']);
    }
}
