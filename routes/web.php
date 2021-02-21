<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::get('/register', 'AuthController@register')->name('register');
    Route::post('/register', 'AuthController@doRegister')->name('auth.register');
    Route::get('/login', 'AuthController@login')->name('login');
    Route::post('/login', 'AuthController@doLogin')->name('auth.login');
    Route::get('/logout', 'AuthController@logout')->name('logout');
});

Route::group(['middleware' => 'login'], function () {
    // 首页
    Route::get('/', 'HomeController@index')->name('home');

    // 项目管理
    Route::group(['prefix' => 'projects'], function () {
        Route::post('/', 'ProjectController@store')->name('projects.store');
        Route::post('/delete', 'ProjectController@delete')->name('projects.delete');
        Route::post('/update', 'ProjectController@update')->name('projects.update');
        Route::get('/{id}', 'ProjectController@show')->name('projects.show')->where('id', '[0-9]+');
        Route::get('/info', 'ProjectController@info')->name('projects.info');
    });

    // 任务列表管理
    Route::group(['prefix' => 'task_list'], function () {
        Route::post('/', 'TaskListController@store')->name('task_list.store');
        Route::get('/{id}', 'TaskListController@show')->name('task_list.show')
            ->where('name', '[0-9]+');
        Route::post('/update', 'TaskListController@update')->name('task_list.update');
        Route::post('/update_sort', 'TaskListController@updateSort')->name('task_list.update_sort');
        Route::post('/delete', 'TaskListController@delete')->name('task_list.delete');
    });

    // 任务管理
    Route::group(['prefix' => 'tasks'], function () {
        Route::post('/', 'TaskController@store')->name('tasks.store');
        Route::post('/update', 'TaskController@update')->name('tasks.update');
        Route::get('/{id}', 'TaskController@show')->name('tasks.show')->where('id', '[0-9]+');
    });
});
