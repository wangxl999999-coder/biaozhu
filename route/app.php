<?php
use think\facade\Route;

Route::get('/', 'index/index');
Route::get('login', 'index/login');
Route::post('login', 'index/login');
Route::get('register', 'index/register');
Route::post('register', 'index/register');
Route::get('logout', 'index/logout');
Route::get('faq', 'index/faq');

Route::group('task', function () {
    Route::get('/', 'task/index');
    Route::get('detail', 'task/detail');
    Route::post('apply', 'task/apply');
    Route::post('submit', 'task/submit');
    Route::post('comment', 'task/comment');
});

Route::group('user', function () {
    Route::get('profile', 'user/profile');
    Route::post('save-education', 'user/saveEducation');
    Route::post('save-work', 'user/saveWork');
    Route::get('center', 'user/center');
    Route::get('earnings', 'user/earnings');
    Route::get('my-tasks', 'user/myTasks');
});

Route::group('admin', function () {
    Route::get('/', 'admin.index/index');
    Route::get('login', 'admin.index/login');
    Route::post('login', 'admin.index/login');
    Route::get('logout', 'admin.index/logout');

    Route::group('user', function () {
        Route::get('/', 'admin.user/index');
        Route::get('detail', 'admin.user/detail');
        Route::post('update-status', 'admin.user/updateStatus');
        Route::post('reset-password', 'admin.user/resetPassword');
    });

    Route::group('task', function () {
        Route::get('/', 'admin.task/index');
        Route::get('add', 'admin.task/add');
        Route::post('add', 'admin.task/add');
        Route::get('edit', 'admin.task/edit');
        Route::post('edit', 'admin.task/edit');
        Route::get('detail', 'admin.task/detail');
        Route::post('audit', 'admin.task/audit');
        Route::get('categories', 'admin.task/categories');
        Route::post('save-category', 'admin.task/saveCategory');
    });

    Route::group('faq', function () {
        Route::get('/', 'admin.faq/index');
        Route::get('add', 'admin.faq/add');
        Route::post('add', 'admin.faq/add');
        Route::get('edit', 'admin.faq/edit');
        Route::post('edit', 'admin.faq/edit');
        Route::post('delete', 'admin.faq/delete');
        Route::get('categories', 'admin.faq/categories');
        Route::post('save-category', 'admin.faq/saveCategory');
    });

    Route::group('config', function () {
        Route::get('/', 'admin.config/index');
        Route::post('/', 'admin.config/index');
    });
});
