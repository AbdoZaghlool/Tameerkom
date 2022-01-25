<?php

use App\Http\Controllers\AdminController\ComplaintController;
use Illuminate\Http\Request;

app()->setLocale('ar');
Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', 'HomeController@index')->name('home');

Route::get('/chat', function () {
    return view('chat');
});

Auth::routes();

Route::get('/get_sub_cat/{model}/{col}/{id}', function ($model, $col, $id) {
    // model_name ,foriegn_key,id
    $new = 'App\\' . $model;
    $data = $new::where($col, $id)->pluck('id', 'name');
    // dd($data);
    return $data;
});

Route::get('category-properties/{id}', 'AdminController\HomeController@catProperties');
Route::get('product-values/{product_id}', 'AdminController\HomeController@productValues');

Route::get('delete-image/{id}','AdminController\HomeController@deleteImage')->name('imageIntroRemove');
Route::redirect('/login', '/admin/login');


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

        // ================================roles====================================
        Route::get('/roles/{id}/delete', 'RoleController@destroy')->name('roles.destroy');
        Route::resource('roles', 'RoleController');
        // ================================application-settings====================================
        Route::get('setting', 'SettingController@index')->name('settings.index');
        Route::post('add/settings', 'SettingController@store')->name('settings.store');

        // ================================about&terms====================================
        Route::get('pages/about', 'PageController@about')->name('about.index');
        Route::post('add/pages/about', 'PageController@store_about')->name('about.store');

        Route::get('pages/terms', 'PageController@terms')->name('terms.index');
        Route::post('add/pages/terms', 'PageController@store_terms')->name('terms.store');

        // ================================main-categories====================================
        Route::get('/main-categories/{category}/delete', 'MainCategoriesController@destroy')->name('main-categories.destroy');
        Route::resource('main-categories', 'MainCategoriesController');

        // ================================regions=============================================
        Route::get('/regions/{id}/delete', 'RegionController@destroy')->name('regions.destroy');
        Route::resource('regions', 'RegionController');

        // ================================cities=============================================
        Route::get('/cities/{id}/delete', 'CityController@destroy')->name('cities.destroy');
        Route::resource('cities', 'CityController');

        // ================================products=============================================
        Route::get('/products/{id}/delete', 'ProductController@destroy');
        // Route::get('/products/{porvider_id?}', 'ProductController@index')->name('products.index');
        Route::get('/products/{id}/delete', 'ProductController@destroy')->name('products.destroy');
        Route::resource('products', 'ProductController');

        // ================================properties=============================================
        Route::get('/properties/{id}/delete', 'PropertyController@destroy')->name('properties.destroy');
        Route::resource('properties', 'PropertyController');

        // ================================property-values=============================================
        Route::get('/property-values/{property}', 'ValueController@index')->name('property-values.index');
        Route::get('/property-values/{property}/create', 'ValueController@create')->name('property-values.create');
        Route::post('/property-values/{property}/store', 'ValueController@store')->name('property-values.store');
        Route::get('/property-values/{id}/delete', 'ValueController@destroy')->name('property-values.destroy');


        // ================================ notifications =============================================
        Route::get('/notifications', 'HomeController@sendNotifications')->name('show-notification-form');
        Route::post('/notifications', 'HomeController@postSendNotifications')->name('post-send-notifications');
        Route::get('/notifications/user', 'HomeController@sendUserNotifications')->name('show-notification-form');
        Route::post('/notifications/user', 'HomeController@postSendUserNotifications')->name('post-send-user-notifications');


        Route::get('/orders/providers-cancele-requests', function () {
            dd('test');
        });

        // ================================orders=============================================
        Route::get('/show-orders/{order}', 'OrdersController@show')->name('orders.show');
        Route::get('/orders/{provider_id?}', 'OrdersController@index')->name('orders.index');
        Route::get('/orders/canceled', 'OrdersController@canceled')->name('orders.canceled');
        Route::post('orders/update-cancele-status', function (Request $request) {
            $order = \App\Order::findOrFail($request->id);
            $order->status = '2';
            $saved = $order->save();
            return $saved ? json_encode('done') : json_encode('error');
        })->name('orders.post-update-cancele-request');



        Route::get('/orders/providers-cancele-requests', 'OrdersController@providersCanceleRequests')->name('orders.cancel-requests');
        Route::get('/orders/{order}/delete', 'OrdersController@delete');


        // =====================================  commissions=========================
        Route::get('commissions', 'OrdersController@commissons')->name('commissions.index');
        Route::get('commissions/paid', 'OrdersController@paid')->name('commissions.paid');
        Route::post('commissions/{id}/update-status', 'OrdersController@postUpdateStatus')->name('commissions.post-update-status');


        // ================================ complaints =============================================
        Route::get('/complaints/{id}/delete', [ComplaintController::class,'delete'])->name('complaints.destroy');
        Route::get('/complaints', [ComplaintController::class,'index'])->name('complaints.index');


        // ================================splashs==============================================
        Route::get('/splashs/{splash}/delete', 'SplashController@destroy')->name('splashs.destroy');
        Route::resource('splashs', 'SplashController');


        // ===================================users============================================
        Route::get('users', 'UserController@index')->name('users.index');
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
