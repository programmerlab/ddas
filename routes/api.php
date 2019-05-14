<?php

use Illuminate\Http\Request;

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

Route::get('checkRoute', function () {
	dd("hfhfhfh");
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/vendor/asd','ApiV2Controller@register');
Route::post('/vendor/lst', 'ApiV2Controller@login');

 

//use Redirect;
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With, auth-token');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Origin: *");
/*
* Rest API Request , auth  & Route
*/

// all route will be like :  base_url/api/v1/{route_name}
// example : localhost.com/api/v1/member/login



Route::group([
	    'prefix' => 'v1'
	], function()
    {
    	 
        Route::match(['post','get'],'member/login', 'ApiController@login');
        Route::match(['post','get'],'member/signup', 'ApiController@register');
		Route::match(['post','get'],'member/updateProfile/{id}', 'ApiController@updateProfile');

        Route::match(['post','get'],'email_verification','ApiController@emailVerification');

        Route::match(['post','get'],'user/forgotPassword','ApiController@forgetPassword');

        Route::match(['post','get'],'password/reset','ApiController@resetPassword');
    // get account
        Route::match(['post','get'],'member/account/{userId}', 'ApiController@myAccount');
        Route::match(['post','get'],'vendor/account/{userId}', 'ApiController@myAccount');
        Route::match(['post','get'],'deliveryBoy/account/{userId}', 'ApiController@myAccount');

        //Rajendra Singh
        Route::match(['post','get'],'member/account/{userId}', 'ApiController@userDetail');

        Route::match(['post','get'],'member/account/myprofile', 'ApiController@getUserDetails');

        Route::match(['post','get'],'vendore/addproduct', 'ApiController@AddVendorProduct');

        Route::match(['post','get'],'vendore/deleteproduct', 'ApiController@destroy');

        Route::match(['post','get'],'product/unit', 'ApiController@getProductUnit');

        Route::match(['post','get'],'product/type', 'ApiController@getProductType');


        // date : 14-05-2019 by kundan

        Route::match(['post','get'],'getCategory','ApiController@getCategory');
        Route::match(['post','get'],'getProductByCategory','ApiController@getProductByCategory');

        

        //end


        // update profile
        Route::match(['post','get'],'vendor/updateProfile/{userId}', 'ApiController@vendorUpdate');
        Route::match(['post','get'],'vendor/updateKyc/{userId}', 'ApiController@updateKyc');

        Route::match(['post','get'],'customer/updateProfile/{userId}', 'ApiController@updateProfile');
        Route::match(['post','get'],'deliveryBoy/updateProfile/{userId}', 'ApiController@updateProfile');


        Route::match(['post','get'],'email_verification','ApiController@emailVerification');
        Route::match(['post','get'],'vendor/product/getAllProduct','ApiController@getVendorProducts');
        Route::match(['post','get'],'vendor/product/updateProductPrice','ApiController@updateProductPrice');
	Route::match(['post','get'],'vendor/product/getOrderHistory','ApiController@getOrderHistory');
	Route::match(['post','get'],'vendor/product/getOrderItems','ApiController@getOrderItems');
	Route::match(['post','get'],'vendor/product/orderAction','ApiController@orderAction');
	Route::match(['post','get'],'member/customerLogin', 'ApiController@customerLogin');
	Route::match(['post','get'],'member/customerChangePassword','ApiController@customerChangePassword');
        Route::match(['post','get'],'member/customerforgotPassword','ApiController@customerforgotPassword');
        Route::match(['post','get'],'member/customerRegistration','ApiController@customerRegistration');
        Route::match(['post','get'],'member/customerSendOtp','ApiController@cutomerSendOTP');
	/*Route::group(['middleware' => 'jwt.auth'], function () {
            Route::match(['post','get'],'member/customerStoreProduct','ApiController@getStoreProduct');
            Route::match(['post','get'],'member/customerStore','ApiController@getAllCustomerStore');
        });*/
	Route::match(['post','get'],'member/customerStoreProduct','ApiController@getStoreProduct');
        Route::match(['post','get'],'member/customerStore','ApiController@getAllCustomerStore');
	Route::match(['post','get'],'member/getAddress','ApiController@getAddress');
	Route::match(['post','get'],'member/placeOrder','ApiController@placeOrder');
	Route::match(['post','get'],'member/myorder','ApiController@getCustomerOrder');
	Route::match(['post','get'],'member/searchproduct','ApiController@searchProduct');
	Route::match(['post','get'],'member/sendnotification','ApiController@sendData');
        //date : 06/12/2018

       // get category
        //Route::match(['post','get'],'vendor/product/getCategory','VendorController@allCategory');
       // search sub categoryby categoryId
        Route::match(['post','get'],'vendor/product/getSubCategoryById/{categoryId}','VendorController@subCategory');
        // get product by id
        Route::match(['post','get'],'vendor/getProductByVendorId/{vendorId}','VendorController@getProduct');

        Route::match(['post','get'],'vendor/showDefaultProducts','VendorController@showDefaultProducts');
	

        // Route::match(['post','get'],'user/forgotPassword','ApiController@forgetPassword');
        // Route::match(['post','get'],'password/reset','ApiController@resetPassword');
        Route::group(['middleware' => 'jwt-auth'], function ()
        {
        	// here you can put your all route which required jwt auth

        	Route::match(['post','get'],'testing',function(){
            
            	die('test');

            }); 
        });

        Route::match(['get','post'],'generateOtp',[
            'as' => 'generateOtp',
            'uses' => 'ApiController@generateOtp'
        ]);

        Route::match(['get','post'],'verifyOtp',[
            'as' => 'verifyOtp',
            'uses' => 'ApiController@verifyOtp'
        ]);
        // if route not found
	    Route::any('{any}', function(){
				$data = [
							'status'=>0,
							'code'=>400,
							'message' => 'Bad request'
						];
				return \Response::json($data);

		});
});


