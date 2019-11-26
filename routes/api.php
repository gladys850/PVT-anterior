<?php

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1',
], function () {
    // Login
    Route::resource('auth', 'Api\V1\AuthController')->only(['store']);
    Route::resource('date', 'Api\V1\DateController')->only(['index']);
    //webcam
    Route::patch('picture/{id}', 'Api\V1\AffiliateController@picture_save');
    // Affiliate
    Route::resource('affiliate', 'Api\V1\AffiliateController')->only(['show']);
    // spouse - affiliate
    Route::get('affiliate/{affiliate_id}/spouse', 'Api\V1\AffiliateController@spouse_get');
    //address - affiliate
    Route::get('affiliate/{affiliate_id}/address', 'Api\V1\AffiliateController@addresses_get');
    Route::patch('affiliate/{affiliate_id}/address', 'Api\V1\AffiliateController@addresses_update');
    //addres
    Route::resource('address', 'Api\V1\AddressController')->only('store', 'destroy', 'update');
    //Spouse
    //Route::resource('spouse', 'Api\V1\SpouseController');
    Route::post('spouse', 'Api\V1\SpouseController@store');
    Route::delete('spouse/{id}', 'Api\V1\SpouseController@destroy');
    Route::patch('spouse/{id}', 'Api\V1\SpouseController@update');
    //beneficiary
    Route::post('beneficiary', 'Api\V1\LoanBeneficiaryController@store');
    Route::get('beneficiary/{id}', 'Api\V1\LoanBeneficiaryController@show');
    Route::delete('beneficiary/{id}', 'Api\V1\LoanBeneficiaryController@destroy');
    Route::patch('beneficiary/{id}', 'Api\V1\LoanBeneficiaryController@update');
    // City
    Route::resource('city', 'Api\V1\CityController')->only(['index']);
    // state
    Route::resource('affiliateState', 'Api\V1\AffiliateStateController')->only(['index']);
    Route::get('AffiliateState/{id}/affiliateStateType', 'Api\V1\AffiliateStateController@get_affiliateStateType');
    // Degree
    Route::resource('degree', 'Api\V1\DegreeController')->only(['index']);
    //pension Entity
    Route::resource('pensionEntity', 'Api\V1\PensionEntityController')->only(['index']);
    //category
    Route::resource('category', 'Api\V1\CategoryController');
    // Fingerprint
    Route::get('affiliate/{id}/fingerprint', 'Api\V1\AffiliateController@fingerprint_saved');
    Route::get('affiliate/{id}/fingerprint_picture', 'Api\V1\AffiliateController@FingerImageprint');
    Route::get('affiliate/{id}/profile_picture', 'Api\V1\AffiliateController@PictureImageprint');
    // Record
    Route::resource('record', 'Api\V1\RecordController')->only(['index']);

    //document
    Route::get('document/{affiliate_id}', 'Api\V1\ScannedDocumentController@create_document');
    Route::resource('procedureDocument', 'Api\V1\ProcedureDocumentController')->only(['index']);


    // With credentials
    Route::group([
        'middleware' => 'jwt.auth'
    ], function () {
        // Logout and refresh token
        Route::resource('auth', 'Api\V1\AuthController')->only(['show', 'update', 'destroy']);
        Route::group([ 'middleware' => 'permission:show-affiliate' ], function () {
            Route::resource('affiliate', 'Api\V1\AffiliateController')->only(['index']);
        });
        Route::group([ 'middleware' => 'permission:create-affiliate' ], function () {
            Route::resource('affiliate', 'Api\V1\AffiliateController')->only(['store']);
        });
        Route::group([ 'middleware' => 'permission:update-affiliate-secondary|update-affiliate-primary' ], function () {
            Route::resource('affiliate', 'Api\V1\AffiliateController')->only(['update']);
            Route::patch('affiliate/{id}/fingerprint', 'Api\V1\AffiliateController@fingerprint_updated');
        });
        Route::group([ 'middleware' => 'permission:delete-affiliate' ], function () {
            Route::resource('affiliate', 'Api\V1\AffiliateController')->only(['destroy']);
        });
       
        // Admin routes
        Route::group([
            'middleware' => 'role:TE-admin'
        ], function () {
            // User
            Route::resource('user', 'Api\V1\UserController');
            Route::get('user/{id}/role', 'Api\V1\UserController@get_roles');
            Route::post('user/{id}/role', 'Api\V1\UserController@set_roles');
            Route::get('user/{id}/permission', 'Api\V1\UserController@get_permissions');
            // Ldap
            Route::get('ldap/unregistered', 'Api\V1\UserController@unregistered_users');
            Route::get('ldap/sync', 'Api\V1\UserController@synchronize_users');
            // Module
            Route::resource('module', 'Api\V1\ModuleController')->only(['index']);
            Route::get('module/{id}/role', 'Api\V1\ModuleController@get_roles');
            // Role
            Route::resource('role', 'Api\V1\RoleController')->only(['index', 'show']);
            Route::get('role/{id}/permission', 'Api\V1\RoleController@get_permissions');
            Route::post('role/{id}/permission', 'Api\V1\RoleController@set_permissions');
            // Permission
            Route::resource('permission', 'Api\V1\PermissionController')->only(['index']);
        });
    });
});