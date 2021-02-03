<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthUserController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\MainController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\HomeController;

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    // Route::get('test-transactions', [HomeController::class, 'test']);
    Route::get('/user/{id}', [MainController::class, 'userById']);
    Route::post('/user-type', [MainController::class, 'userType']);
    Route::post('/order-type', [MainController::class, 'orderType']);


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

    Route::get('/splashs', [MainController::class, 'splashs']);
    Route::get('/regions', [MainController::class, 'regions']);
    Route::get('/cities/{region_id}', [MainController::class, 'cities']);
    Route::get('/types', [MainController::class, 'types']);
    Route::get('/topics/{topic_id}/{lat}/{long}', [MainController::class, 'topics']);
    Route::get('/all-topics', [MainController::class, 'allTopics']);
    Route::get('/all-categories', [MainController::class, 'allCategories']);
    Route::get('/categories/{provider_id}', [MainController::class, 'mainCat']);
    Route::get('/families/{lat}/{long}/{filter}', [MainController::class, 'families']);

    Route::post('check-discount-code', [MainController::class, 'checkDiscountCode']);


    Route::get('/about-us', [ProfileController::class, 'about_us'])->name('about_us');
    Route::get('/terms-and-conditions', [ProfileController::class, 'terms_and_conditions'])->name('terms_and_conditions');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::post('/search', [MainController::class, 'search']);
    Route::post('/contact-us', [ProfileController::class, 'contacts']);


    /**
     *  this section the user must be logged in.
     */
    Route::group(['middleware' => ['auth:api', 'cors']], function () {

        /** change user information */
        Route::post('/user-change-password', [AuthUserController::class, 'changePassword'])->name('user_changePassword');
        Route::post('/user-change-phone-number', [AuthUserController::class, 'change_phone_number'])->name('user_change_phone_number');
        Route::post('/user-check-code-change-phone-number', [AuthUserController::class, 'check_code_changeNumber'])->name('user_check_code_changeNumber');
        Route::post('/user-change-info', [AuthUserController::class, 'changeInfo'])->name('user_change_info');
        Route::post('/user-update-info', [AuthUserController::class, 'updateInfo'])->name('user-change-info');
        Route::post('/change-image', [UserController::class, 'change_image'])->name('change_image');
        Route::get('/my-rate', [ProfileController::class, 'myRate'])->name('getRate');

        //===============logout========================
        Route::post('/user-logout', [AuthUserController::class, 'logout'])->name('user-logout');

        Route::post('/family-update-info', [PartnerController::class,'updateInfo']);
        Route::post('/driver-update-info', [DriverController::class,'updateInfo']);

        /*notifications*/
        Route::get('/list-notifications', [ApiController::class, 'listNotifications']);
        Route::post('/read_all_Notifications', [ApiController::class, 'read_all_notification']);
        Route::post('/delete_Notifications/{id}', [ApiController::class, 'delete_Notifications']);


        /*addresses*/
        Route::get('/get-addresses', [AddressController::class, 'index']);
        Route::post('/add-address', [AddressController::class, 'store']);
        Route::post('/edit-address/{id}', [AddressController::class, 'update']);
        Route::post('/delete-address/{id}', [AddressController::class, 'destroy']);

        // available status
        Route::post('user/change-available', [MainController::class, 'updateAvailable']);

        // user histories & statistics
        Route::get('histories', [ProfileController::class, 'histories']);
        Route::get('my-orders-stats', [ProfileController::class, 'stats']);

        /*user wallet*/
        Route::post('charge-wallet', [ProfileController::class, 'charge_electronic_pocket']);
        Route::get('get-wallet', [ProfileController::class, 'getWallet']);
        Route::post('pull-request', [ProfileController::class, 'pullMoney']);


        /*cart*/
        Route::get('/my-cart-items', [CartController::class, 'index']);
        Route::post('/add-to-cart', [CartController::class, 'store']);
        Route::post('/add-or-update-cart', [CartController::class, 'update']);
        Route::post('/remove-from-cart/{product_id}', [CartController::class, 'destroy']);
        Route::post('/empty-cart', [CartController::class, 'empty']);


        /**post order*/
        Route::post('post-current-orders', [OrderController::class, 'currentOrders']);
        Route::post('recieve-order-myself', [OrderController::class, 'RecieveOrderMyself']);
        Route::post('compelete-order', [OrderController::class, 'compeleteOrder']);
        Route::post('finish-order', [OrderController::class, 'finishOrder']);
        Route::post('cancele-order', [OrderController::class, 'canceleOrder']);
        Route::post('post-complaint', [OrderController::class, 'postComplaint']);
        Route::post('post-scheduled-orders', [OrderController::class, 'scheduledOrders']);
        Route::post('/refuse-offer', [OrderController::class,'refuesOffer']);
        Route::post('/notify-user', [OrderController::class,'notifyUser']);


        /*user orders*/
        Route::get('/user-current-orders', [UserController::class, 'myCurrentOrders']);
        Route::post('/user-scheduled-orders', [UserController::class, 'myScheduledOrders']);
        Route::get('/user-finished-orders', [UserController::class, 'finishedOrders']);
        Route::get('/user-order-offers/{order_id}', [UserController::class, 'orderOffers']);
        Route::post('/user-accept-offer', [UserController::class, 'acceptOrderOffer']);
        Route::post('/user-change-scheduled-order-time', [UserController::class,'changeScheduledOrderTime']);
        Route::post('/rate-product', [UserController::class,'rateProduct']);
        Route::post('/rate', [OrderController::class,'rates']);

        /*family store*/
        Route::get('view-my-store', [PartnerController::class,'viewMyStore']);

        /*family orders*/
        Route::get('/family-orders', [PartnerController::class, 'myOrders']);
        Route::post('/family-scheduled-orders', [PartnerController::class, 'myScheduledOrders']);
        Route::post('family-update-work-time', [PartnerController::class, 'updateWorkTime']);
        Route::post('/family-refuse-scheduled-orders', [PartnerController::class,'refuseScheduledOrders']);
        Route::post('/family-accept-scheduled-orders', [PartnerController::class,'acceptScheduledOrders']);

        /** family comments and rates */
        Route::get('comments-rates', [PartnerController::class,'usersComments']);
        Route::get('produt-comments-rates', [PartnerController::class,'productsComments']);

        /** family subscriptions */
        Route::get('/services', [MainController::class, 'services']);
        Route::post('family-subscribe-service', [PartnerController::class,'subscribe']);

        /** family sliders */
        Route::get('family-get-sliders', [SliderController::class,'index']);
        Route::post('family-create-slider', [SliderController::class,'store']);
        Route::post('family-delete-slider-by-id/{id}', [SliderController::class,'destroy']);

        /** family coupons */
        Route::get('family-my-coupons', [CouponController::class,'index']);
        Route::post('family-create-coupon', [CouponController::class,'store']);
        Route::post('family-update-coupon/{id}', [CouponController::class,'update']);
        Route::post('family-delete-coupon', [CouponController::class,'destroy']);

        /*driver orders*/
        Route::post('/driver-current-orders', [DriverController::class, 'myCurrentOrders']);
        Route::post('/driver-scheduled-orders', [DriverController::class, 'myScheduledOrders']);
        Route::post('/driver-add-offer', [DriverController::class, 'addOffer']);
        Route::post('/order-offer', [DriverController::class, 'orderOffer']);

        //=========================products============================
        Route::get('my-products', [ProductController::class, 'index']);
        Route::post('add-product', [ProductController::class, 'store']);
        Route::post('update-product/{product}', [ProductController::class, 'update']);
        Route::post('delete-product/{product}', [ProductController::class, 'destroy']);
        Route::post('delete-prodcut-image-by-id/{image_id}', [ProductController::class, 'deleteImageById']);
        Route::post('delete-prodcut-addition-by-id/{add_id}', [ProductController::class, 'deleteAdditionById']);
    });
});
