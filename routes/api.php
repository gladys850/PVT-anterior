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
    Route::get('affiliate/{id}/degree_name', 'Api\V1\AffiliateController@get_degree');
    Route::get('affiliate/{id}/category_name', 'Api\V1\AffiliateController@get_category');
    Route::get('affiliate/{id}/unit_name', 'Api\V1\AffiliateController@get_unit');
    Route::get('affiliate/{id}/state', 'Api\V1\AffiliateController@get_state');
    // spouse - affiliate
    Route::get('affiliate/{affiliate_id}/spouse', 'Api\V1\AffiliateController@spouse_get');
    //address - affiliate
    Route::get('affiliate/{affiliate_id}/address', 'Api\V1\AffiliateController@addresses_get');
    Route::patch('affiliate/{affiliate_id}/address', 'Api\V1\AffiliateController@addresses_update');
    //addres
    Route::resource('address', 'Api\V1\AddressController')->only('store', 'destroy', 'update');
    //Spouse
    Route::resource('spouse', 'Api\V1\SpouseController')->only('store', 'destroy', 'update');
    //beneficiary
    Route::resource('beneficiary', 'Api\V1\LoanBeneficiaryController')->only('index', 'store', 'show', 'destroy', 'update');
    //loan_request
    Route::resource('pre_request', 'Api\V1\LoanRequestController')->only(['index', 'store', 'show', 'destroy']);
    Route::get('pdf/{id}/pre_request', 'Api\V1\LoanRequestController@createpdf');
    //form_request
    Route::get('pdf_request/{id}', 'Api\V1\LoanController@create_request');
    // City
    Route::resource('city', 'Api\V1\CityController')->only(['index']);
    // state
    Route::resource('affiliate_state', 'Api\V1\AffiliateStateController')->only(['index']);
    // Degree
    Route::resource('degree', 'Api\V1\DegreeController')->only(['index']);
    //pension Entity
    Route::resource('pension_entity', 'Api\V1\PensionEntityController')->only(['index', 'show']);
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
    //Loan
    Route::resource('loan', 'Api\V1\LoanController')->only(['index']);
    Route::resource('loan', 'Api\V1\LoanController')->only(['show']);
    Route::resource('loan', 'Api\V1\LoanController')->only(['store']);
    Route::resource('loan', 'Api\V1\LoanController')->only(['destroy']);
    Route::resource('loan', 'Api\V1\LoanController')->only(['update']);
    //affiliate lender loans
    Route::get('affiliate/{id}/loan','Api\V1\AffiliateController@get_loans');
    //verify if an affiliate can be guarantor
    Route::get('affiliate/{id}/verify_guarantor','Api\V1\AffiliateController@verify_guarantor');
    // Procedure Type
    Route::resource('procedure_type', 'Api\V1\ProcedureTypeController')->only(['index', 'show']);
    // Procedure Modality
    Route::resource('procedure_modality', 'Api\V1\ProcedureModalityController')->only(['index', 'show']);
    //list of requirements for procedure modalities
    Route::get('procedure_modality/{id}/requirements', 'Api\V1\ProcedureModalityController@get_requirements');
    //submitted_documents
    Route::get('loan/{loan_id}/submitted_documents', 'Api\V1\LoanController@submitted_documents');
    Route::get('affiliate/{id}/contribution', 'Api\V1\AffiliateController@get_contributions');
    // verify cpop 
    Route::get('affiliate/{id}/cpop','Api\V1\AffiliateController@cpop');
    //LoanIntervals
    Route::resource('loan_interval', 'Api\V1\LoanIntervalController')->only(['index']);
    // With credentials
    Route::group([
        'middleware' => 'auth'
    ], function () {
        // Logout and refresh token
        Route::resource('auth', 'Api\V1\AuthController')->only(['index']);
        Route::delete('auth', 'Api\V1\AuthController@logout');
        Route::patch('auth', 'Api\V1\AuthController@refresh');
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
            Route::resource('module', 'Api\V1\ModuleController')->only(['index', 'show']);
            Route::get('module/{id}/role', 'Api\V1\ModuleController@get_roles');
            Route::get('module/{id}/procedure_type', 'Api\V1\ModuleController@get_procedure_types');
            // Role
            Route::resource('role', 'Api\V1\RoleController')->only(['index', 'show']);
            Route::get('role/{id}/permission', 'Api\V1\RoleController@get_permissions');
            Route::post('role/{id}/permission', 'Api\V1\RoleController@set_permissions');
            // Permission
            Route::resource('permission', 'Api\V1\PermissionController')->only(['index']);
        });
    });
});
