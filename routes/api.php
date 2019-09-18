<?php

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1',
], function () {
    // Login
    Route::resource('auth', 'Api\V1\AuthController')->only(['store']);
    Route::resource('date', 'Api\V1\DateController')->only(['show']);

    // With credentials
    Route::group([
        'middleware' => 'jwt.auth'
    ], function () {
        // Logout and refresh token
        Route::resource('auth', 'Api\V1\AuthController')->only(['show', 'update', 'destroy']);

        // Admin routes
        Route::group([
            'middleware' => 'role:Administrador'
        ], function () {
            // User
            Route::resource('user', 'Api\V1\UserController')->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::get('user/{id}/role', 'Api\V1\UserController@get_roles');
            Route::post('user/{id}/role', 'Api\V1\UserController@set_roles');
            // Ldap
            Route::get('ldap', 'Api\V1\UserController@unregistered_users');
            // Module
            Route::resource('module', 'Api\V1\ModuleController')->only(['index']);
            Route::get('module/{id}/role', 'Api\V1\ModuleController@get_roles');
            // Role
            Route::resource('role', 'Api\V1\RoleController')->only(['index']);
             // Affiliate
            Route::resource('affiliate', 'Api\V1\AffiliateController')->only(['index','store','show','update','destroy']);
        });
    });
});
