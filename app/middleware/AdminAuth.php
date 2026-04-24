<?php
namespace app\middleware;

use think\facade\Session;
use think\Response;

class AdminAuth
{
    public function handle($request, \Closure $next): Response
    {
        if (!Session::has('admin_id')) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
