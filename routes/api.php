<?php

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1',
], function () {
    // Login
    Route::resource('auth', 'Api\V1\AuthController')->only(['store']);
    Route::resource('date', 'Api\V1\DateController')->only(['index']);

    // With credentials
    Route::group([
        'middleware' => 'jwt.auth'
    ], function () {
        // Logout and refresh token
        Route::resource('auth', 'Api\V1\AuthController')->only(['show', 'update', 'destroy']);
        // Admin routes
        Route::group([
            'middleware' => 'role:admin'
        ], function () {
            // User
            Route::resource('user', 'Api\V1\UserController')->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::get('user/{id}/role', 'Api\V1\UserController@get_roles');
            Route::post('user/{id}/role', 'Api\V1\UserController@set_roles');
            Route::get('user/{id}/permission', 'Api\V1\UserController@get_permissions');
            // Ldap
            Route::get('ldap', 'Api\V1\UserController@unregistered_users');
            // Module
            Route::resource('module', 'Api\V1\ModuleController')->only(['index']);
            Route::get('module/{id}/role', 'Api\V1\ModuleController@get_roles');
            // Role
            Route::resource('role', 'Api\V1\RoleController')->only(['index', 'show']);
            Route::get('role/{id}/permission', 'Api\V1\RoleController@get_permissions');
            // Permission
            Route::resource('permission', 'Api\V1\PermissionController')->only(['index']);
             // Affiliate
            Route::resource('affiliate', 'Api\V1\AffiliateController')->only(['index','store','show','update','destroy']);
        });
    });
});
