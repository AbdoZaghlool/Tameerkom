<?php

use App\Http\Controllers\AdminController\ComplaintController;
app()->setLocale('ar');
Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/get-filterd-users/{id?}', 'AdminController\HomeController@filterdUsers') ;

Route::get('/get-orders-status', 'AdminController\HomeController@ordersStats') ;

Route::get('/get_sub_cat/{model}/{col}/{id}', function ($model, $col, $id) {
    // model_name ,foriegn_key,id
    $new = 'App\\' . $model;
    $data = $new::where($col, $id)->pluck('id', 'name');
    // dd($data);
    return $data;
});


Route::redirect('/login', '/admin/login');

Route::get('/check-status/{id?}/{id1?}', 'Api\ProfileController@fatooraStatus');
Route::get('/order-check-status/{id?}/{id1?}', 'Api\OrderController@fatooraStatus');
Route::get('/fatoora/success', function () {
    return view('fatoora');
})->name('fatoora-success');
Route::get('/fatoora/error', function () {
    return view('fatoora-error');
})->name('fatoora-error');

/*admin panel routes*/

Route::get('/admin/home', ['middleware' => 'auth:admin', 'uses' => 'AdminController\HomeController@index'])->name('admin.home');

Route::prefix('admin')->group(function () {
    Route::get('login', 'AdminController\Admin\LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'AdminController\Admin\LoginController@login')->name('admin.login.submit');
    Route::get('password/reset', 'AdminController\Admin\ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('password/email', 'AdminController\Admin\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('password/reset/{token}', 'AdminController\Admin\ResetPasswordController@showResetForm')->name('admin.password.reset');
    Route::post('password/reset', 'AdminController\Admin\ResetPasswordController@reset')->name('admin.password.update');
    Route::post('logout', 'AdminController\Admin\LoginController@logout')->name('admin.logout');


    Route::group(['middleware' => ['web', 'auth:admin'], 'namespace' => 'AdminController'], function () {

        // ================================application-settings====================================
        Route::get('setting', 'SettingController@index');
        Route::post('add/settings', 'SettingController@store');


        // ================================vats====================================
        Route::get('vats', 'HomeController@vat');

        // ================================about&terms====================================
        Route::get('pages/about', 'PageController@about');
        Route::post('add/pages/about', 'PageController@store_about');

        Route::get('pages/terms', 'PageController@terms');
        Route::post('add/pages/terms', 'PageController@store_terms');


        // ================================subscriptions====================================
        Route::get('subscriptions', 'HomeController@subscriptions')->name('subscriptions.index');


        //================================contacts===========================================
        Route::get('/contacts', 'ContactsController@index')->name('contacts.index');
        Route::get('/contacts/{contact}', 'ContactsController@show');
        Route::get('/contact/reply', 'ContactsController@reply');
        Route::get('/contacts/{contact}/delete', 'ContactsController@delete');


        //================================topics===========================================
        Route::get('/topics/{id}/delete', 'TopicController@destroy');
        Route::resource('topics', 'TopicController');


        // ================================main-categories====================================
        Route::get('/main-categories/{category}/delete', 'MainCategoriesController@destroy');
        Route::resource('main-categories', 'MainCategoriesController');


        // ================================regions=============================================
        Route::get('/regions/{id}/delete', 'RegionController@destroy');
        Route::resource('regions', 'RegionController');


        // ================================cities=============================================
        Route::get('/cities/{id}/delete', 'CityController@destroy');
        Route::resource('cities', 'CityController');


        // ================================ notifications =============================================
        Route::get('/notifications', 'HomeController@sendNotifications');
        Route::post('/notifications', 'HomeController@postSendNotifications')->name('post-send-notifications');
        Route::get('/notifications/user', 'HomeController@sendUserNotifications');
        Route::post('/notifications/user', 'HomeController@postSendUserNotifications')->name('post-send-user-notifications');


        // ================================ verifications =============================================
        Route::get('/complaints/{id}/delete', [ComplaintController::class,'delete'])->name('complaints.delete');
        Route::get('/complaints', [ComplaintController::class,'index'])->name('complaints.index');

        // ================================ wallets ==========================================
        Route::get('/charge-wallet', 'HomeController@getChargeWallet')->name('chargeWallet');
        Route::post('/charge-wallet', 'HomeController@postChargeWallet')->name('chargeWalletPost');
        Route::get('/pull-requests', 'HomeController@getPullRequests')->name('pullRequests.index');
        Route::get('/pull-requests/{id}/update', 'HomeController@postPullRequests')->name('PullRequests.update');

        // ================================services==========================================
        Route::get('/services/{service}/delete', 'ServicesController@destroy');
        Route::resource('services', 'ServicesController');


        // ================================orders=============================================
        Route::get('/orders', 'OrdersController@index')->name('orders.index');
        Route::get('/orders/canceled', 'OrdersController@canceled')->name('orders.canceled');
        Route::get('/orders/{order}', 'OrdersController@show')->name('orders.show');
        Route::get('/orders/{order}/delete', 'OrdersController@delete');


        // ================================splashs==============================================
        Route::get('/splashs/{splash}/delete', 'SplashController@destroy');
        Route::resource('splashs', 'SplashController');


        // ===================================users============================================
        Route::get('users', 'UserController@index')->name('users.index');
        Route::get('users/drivers', 'UserController@drivers')->name('drivers.index');
        Route::get('users/providers', 'UserController@providers')->name('providers.index');
        Route::get('users/create/{type}', 'UserController@create')->name('users.create');
        Route::post('users/{type}', 'UserController@store')->name('users.store');
        Route::get('users/{id}/edit', 'UserController@edit')->name('users.edit');
        Route::post('users/{id}/update/{type}', 'UserController@update')->name('users.update');
        Route::get('delete/{id}/user', 'UserController@destroy')->name('users.delete');
        Route::get('update/pass/{id}', 'UserController@update_pass')->name('users.update-pass');
        Route::post('update/privacy/{id}', 'UserController@update_privacy');
        Route::get('update/blocked/{id}', 'UserController@updateBlocked');


        //=========================admins-profile=================================================
        Route::resource('admins', 'AdminController');
        Route::get('/profile', 'AdminController@my_profile')->name('my_profile');
        Route::post('/profileEdit', 'AdminController@my_profile_edit')->name('my_profile_edit');
        Route::get('/profileChangePass', 'AdminController@change_pass')->name('change_pass');
        Route::post('/profileChangePass', 'AdminController@change_pass_update')->name('change_pass');
        Route::get('/admin_delete/{id}', 'AdminController@admin_delete')->name('admin_delete');
    });
});
