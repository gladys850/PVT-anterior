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
            // Ldap
            Route::resource('ldap', 'Api\V1\LdapController')->only(['index']);
        });
    });
});
