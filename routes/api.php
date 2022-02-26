<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => ['set-header'], 'prefix' => 'v1'], function () {

    Route::get('welcome', 'Controller@welcome');

    /** Users Role */
    Route::group(['prefix' => 'onboard'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('AdminLogin', 'Auth\OnboardingController@adminLogin');

            Route::post('customerLogin', 'Auth\OnboardingController@customerLogin');
        });
    });


    Route::group(['middleware' => ['access-control']], function () {
        #The authenticated parts
        Route::group(['middleware' => 'auth:api'], function ()
        {
            #The authenticated parts
            Route::group(['middleware' => ['auth-admin'], 'prefix' => 'admins'], function ()
            {
                Route::post('create', 'AdminController@createCustomer');
                Route::post('subCreate', 'AdminController@createSubAccountForExistingCustomer');
                Route::get('pull/all', 'AdminController@getCustomers');
            });

            #The authenticated parts
            Route::group(['middleware' => ['auth-customer'], 'prefix' => 'customers'], function ()
            {
                Route::post('transfer', 'CustomerController@transferMoney');
                Route::get('pull/all', 'CustomerController@getCustomerAccounts');
            });

        });
    });

});
