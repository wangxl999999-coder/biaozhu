<?php
namespace app\middleware;

use think\facade\Session;
use think\Response;

class Auth
{
    public function handle($request, \Closure $next): Response
    {
        if (!Session::has('user_id')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
