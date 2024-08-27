<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('settings', 'SettingController@web');
    $router->resource('messages', MessageController::class);
    $router->resource('groups', GroupController::class);
    $router->resource('actions', ActionController::class);
    $router->resource('washes', WashController::class);

    $router->resource('projects', ProjectController::class);
    $router->resource('additions', AdditionController::class);
});
