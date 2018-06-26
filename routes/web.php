<?php

$router->group(['prefix' => 'api',], function ($router) {
    //不需要登录的
    $router->post('login', 'UsersController@login');

    //要登录
    $router->group(['middleware' => 'auth:api'], function ($router) {
        $router->post('getUser', 'UsersController@getUser');
        $router->post('logout', 'UsersController@logout');
        $router->post('refresh', 'UsersController@refresh');
    });

});

