<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthUserController;
use App\Http\Controllers\Api\MainController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {

    // ==========================authentication requests=====================================
    Route::group(['middleware' =>  ['cors', 'api']], function () {
        /**register mobile */
        Route::post('/register-mobile', [AuthUserController::class, 'registerMobile'])->name('register-mobile');
        Route::post('/phone-verification', [AuthUserController::class, 'register_phone_post'])->name('register_phone_post');
        Route::post('/resend-code', [AuthUserController::class, 'resend_code'])->name('resend_code');

        /*user register*/
        Route::post('/user-register', [AuthUserController::class, 'register'])->name('register');
        Route::post('/user-login', [AuthUserController::class, 'login'])->name('user-login');
        Route::post('/user-forget-password', [AuthUserController::class, 'forgetPassword'])->name('forgetPassword');
        Route::post('/user-confirm-reset-code', [AuthUserController::class, 'confirmResetCode'])->name('user-confirmResetCode');
        Route::post('/user-reset-password', [AuthUserController::class, 'resetPassword'])->name('user-resetPassword');
    });



    Route::get('/user/{id}', [MainController::class, 'userById']);
    Route::post('/user-type', [MainController::class, 'userType']);

    Route::get('/splashs', [MainController::class, 'splashs']);
    Route::get('/regions', [MainController::class, 'regions']);
    Route::get('/cities/{region_id}', [MainController::class, 'cities']);
    Route::get('/categories/{category_id?}', [MainController::class, 'mainCat']);



    Route::get('/about-us', [ProfileController::class, 'about_us'])->name('about_us');
    Route::get('/terms-and-conditions', [ProfileController::class, 'terms_and_conditions'])->name('terms_and_conditions');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::post('/search', [MainController::class, 'search']);


    /**
     *  this section the user must be logged in.
     */
    Route::group(['middleware' => ['auth:api', 'cors']], function () {

        /** change user information */
        Route::post('/user-change-password', [AuthUserController::class, 'changePassword'])->name('user_changePassword');
        Route::post('/user-change-phone-number', [AuthUserController::class, 'change_phone_number'])->name('user_change_phone_number');
        Route::post('/user-check-code-change-phone-number', [AuthUserController::class, 'check_code_changeNumber'])->name('user_check_code_changeNumber');
        Route::post('/user-change-info', [AuthUserController::class, 'changeInfo'])->name('user_change_info');
        Route::get('/my-rate', [ProfileController::class, 'myRate'])->name('getRate');

        //===============logout========================
        Route::post('/user-logout', [AuthUserController::class, 'logout'])->name('user-logout');

        /*notifications*/
        Route::get('/list-notifications', [ApiController::class, 'listNotifications']);
        Route::post('/read_all_Notifications', [ApiController::class, 'read_all_notification']);
        Route::post('/delete_Notifications/{id}', [ApiController::class, 'delete_Notifications']);


        // user favourites
        Route::get('get-favourite-providers', [UserController::class,'getFavouriteProviders']);
        Route::post('favourite-provider', [UserController::class,'favouriteProvider']);
        Route::post('unfavourite-provider', [UserController::class,'unFavouriteProvider']);
        Route::get('get-favourite-products', [UserController::class,'getFavouriteProducts']);
        Route::post('favourite-product', [UserController::class,'favouriteProduct']);
        Route::post('unfavourite-product', [UserController::class,'unFavouriteProduct']);
        
        // user histories & statistics
        Route::get('histories', [ProfileController::class, 'histories']);
        Route::get('my-orders-stats', [ProfileController::class, 'stats']);


        /**post order*/
        Route::post('order', [OrderController::class, 'postOrder']);
        Route::post('compelete-order', [OrderController::class, 'compeleteOrder']);
        Route::post('cancele-order', [OrderController::class, 'canceleOrder']);
        Route::post('post-complaint', [OrderController::class, 'postComplaint']);


        /*user orders*/
        Route::get('/my-orders', [UserController::class, 'myOrders']);
        Route::post('/rate-product', [UserController::class,'rateProduct']);
        Route::post('/rate', [OrderController::class,'rates']);

        /*provider orders*/
        Route::get('/provider-orders', [PartnerController::class, 'myOrders']);
        Route::post('upload-payment-image', [PartnerController::class, 'uploadImage']);

        /** family comments and rates */
        Route::get('comments-rates', [PartnerController::class,'usersComments']);


        //=========================products============================
        Route::get('my-products', [ProductController::class, 'index']);
        Route::post('add-product', [ProductController::class, 'store']);
        Route::post('update-product/{product}', [ProductController::class, 'update']);
        Route::post('delete-product/{product}', [ProductController::class, 'destroy']);
        Route::post('delete-prodcut-image-by-id/{image_id}', [ProductController::class, 'deleteImageById']);
    });
});