<?php
use think\facade\Route;

Route::get('/', 'Index/index');
Route::get('login', 'Index/login');
Route::post('login', 'Index/login');
Route::get('register', 'Index/register');
Route::post('register', 'Index/register');
Route::get('logout', 'Index/logout');
Route::get('faq', 'Index/faq');

Route::get('task', 'Task/index');
Route::get('task/detail', 'Task/detail');
Route::post('task/apply', 'Task/apply');
Route::post('task/submit', 'Task/submit');
Route::post('task/comment', 'Task/comment');

Route::get('user/profile', 'User/profile');
Route::post('user/save-education', 'User/saveEducation');
Route::post('user/save-work', 'User/saveWork');
Route::get('user/center', 'User/center');
Route::get('user/earnings', 'User/earnings');
Route::get('user/my-tasks', 'User/myTasks');

Route::get('admin', 'admin\Index/index');
Route::get('admin/login', 'admin\Index/login');
Route::post('admin/login', 'admin\Index/login');
Route::get('admin/logout', 'admin\Index/logout');

Route::get('admin/user', 'admin\User/index');
Route::get('admin/user/detail', 'admin\User/detail');
Route::post('admin/user/update-status', 'admin\User/updateStatus');
Route::post('admin/user/reset-password', 'admin\User\resetPassword');

Route::get('admin/task', 'admin\Task/index');
Route::get('admin/task/add', 'admin\Task/add');
Route::post('admin/task/add', 'admin\Task/add');
Route::get('admin/task/edit', 'admin\Task/edit');
Route::post('admin/task/edit', 'admin\Task/edit');
Route::get('admin/task/detail', 'admin\Task/detail');
Route::post('admin/task/audit', 'admin\Task/audit');
Route::get('admin/task/categories', 'admin\Task/categories');
Route::post('admin/task/save-category', 'admin\Task/saveCategory');

Route::get('admin/faq', 'admin\Faq/index');
Route::get('admin/faq/add', 'admin\Faq/add');
Route::post('admin/faq/add', 'admin\Faq/add');
Route::get('admin/faq/edit', 'admin\Faq/edit');
Route::post('admin/faq/edit', 'admin\Faq/edit');
Route::post('admin/faq/delete', 'admin\Faq/delete');
Route::get('admin/faq/categories', 'admin\Faq/categories');
Route::post('admin/faq/save-category', 'admin\Faq/saveCategory');

Route::get('admin/config', 'admin\Config/index');
Route::post('admin/config', 'admin\Config/index');
