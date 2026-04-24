<?php
namespace app\controller\admin;

use app\BaseController;
use app\model\Admin;
use app\model\Config as ConfigModel;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;

class Config extends BaseController
{
    protected $middleware = [\app\middleware\AdminAuth::class];

    public function index()
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);
        View::assign('admin', $admin);

        $configs = ConfigModel::select();
        
        $configData = [];
        foreach ($configs as $config) {
            $configData[$config->key] = $config->value;
        }

        View::assign('configData', $configData);

        if (Request::isPost()) {
            $postData = Request::post();
            
            foreach ($postData as $key => $value) {
                $config = ConfigModel::where('key', $key)->find();
                if ($config) {
                    $config->value = $value;
                    $config->update_time = time();
                    $config->save();
                } else {
                    $newConfig = new ConfigModel();
                    $newConfig->key = $key;
                    $newConfig->value = $value;
                    $newConfig->create_time = time();
                    $newConfig->update_time = time();
                    $newConfig->save();
                }
            }

            return json(['code' => 1, 'msg' => '配置保存成功']);
        }

        return View::fetch();
    }
}
