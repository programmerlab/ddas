<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Log\Writer;
use Monolog\Logger as Monolog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Config,Mail,View,Redirect,Validator,Response;
use Auth,Crypt,Hash,Lang,JWTAuth,Input,Closure,URL;
use JWTExceptionTokenInvalidException;
use App\Helpers\Helper as Helper;
use Modules\Admin\Models\User;
use Modules\Admin\Models\Vendors as Vendor;
use Modules\Admin\Models\Kyc;
use Modules\Admin\Models\Category;
use App\Models\Notification;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Dispatcher;
use Cookie;
use Modules\Admin\Models\Product;
use Modules\Admin\Models\ProductType;
use Modules\Admin\Models\ProductUnit;
use Modules\Admin\Models\VendorProduct;


class ApiController extends Controller {
    /* @method : validateUser
     * @param : email,password,firstName,lastName
     * Response : json
     * Return : token and user details
     * Author : kundan Roy
     * Calling Method : get
     */

    public $sid = "";
    public $token = "";
    public $from = "";
    private $distance = 5;
    private $serverLKey = 'AIzaSyAE3lsAMR2YeIzksyrVKo8SgmfD3ySQ1pQ';
    private $fcm_url = 'https://fcm.googleapis.com/fcm/send';

    public function __construct(Request $request) {
        if ($request->header('Content-Type') != "application/json") {
            $request->headers->set('Content-Type', 'application/json');
        }
        $user_id = $request->input('userId');
    }


    public function getProductByCategory(){ 
       // $cd = CategoryDashboard::all 
        $category_data =  \DB::table('marketplace_category_product')->where('category_id','2')->pluck('product_id');

         
         
        if(count($category_data)){
            $status = 1;
            $code   = 200;
            $msg    = "Category  list";



        }else{
            $status = 0;
            $code   = 404;
            $msg    = "Category  list not  found!";
        }
        return  response()->json([ 
                "status"=>$status,
                "code"=> $code,
                "message"=> $msg,
                'data' => $category_data
            ]
        );
    }

    // get category
    public function getCategory(){ 
       // $cd = CategoryDashboard::all 
        $category_data =  \DB::table('marketplace_categories')->select('id','name as categoryName')->get();
         
        if(count($category_data)){
            $status = 1;
            $code   = 200;
            $msg    = "Category  list";
        }else{
            $status = 0;
            $code   = 404;
            $msg    = "Category  list not  found!";
        }
        return  response()->json([ 
                "status"=>$status,
                "code"=> $code,
                "message"=> $msg,
                'data' => $category_data
            ]
        );
    }

    // deactive user
    public function deactivateUser($user_id = null) {
        $user = User::find($user_id);
        /** Return Error Message * */
        if (!$user) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'code' => 500,
                        'message' => 'Invalid User id',
                        'data' => $request->all()
                            )
            );
        }
        $user->status = 0;
        $user->save();
        return Response::json(array(
                    'status' => 1,
                    'code' => 200,
                    'message' => 'Account deativated',
                    'data' => []
                        )
        );
    }

    public function register(Request $request, User $user) {
        // social media
        $authType = $request->get('authType');
        // auth type
        if ($authType == 'google' || $authType == 'facebook' || $authType == 'twitter' || $authType == 'linkedin') {
            //Server side valiation
            $validator = Validator::make($request->all(), [
                        'firstName' => 'required',
                        'email' => 'required',
                        'providerId' => 'required',
                        'loginType' => 'required',
                        'authType' => 'required'
            ]);
        } else {
            //Server side valiation
            $validator = Validator::make($request->all(), [
                        'firstName' => 'required',
                        'email' => 'required|email|unique:users',
                        'password' => 'required',
                        'loginType' => 'required',
            ]);
        }
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'code' => 201,
                        'message' => $error_msg[0],
                        'data' => $request->all()
                            )
            );
        }
        $loginType = $request->get('loginType');
        $role = Config::get('role');
        $roleType = (object) array_flip($role);
        $input['first_name'] = $request->get('firstName');
        $input['last_name'] = $request->get('lastName');
        $input['email'] = $request->get('email');
        $input['username'] = $request->get('username');
        $input['password'] = Hash::make($request->get('password'));
        $input['role_type'] = $roleType->$loginType;
        $input['user_type'] = $request->get('authType'); // social media
        $input['provider_id'] = $request->get('providerId');
        $helper = new Helper;
        /** --Create user-- * */
        $user = User::create($input);
        $subject = "Welcome to ventrega! Verify your email address to get started";
        $email_content = [
            'receipent_email' => $request->get('email'),
            'subject' => $subject,
            'greeting' => 'Ventrega',
            'first_name' => $request->get('firstName'),
            'first_name' => $request->get('firstName')
        ];
        $verification_email = $helper->sendMailFrontEnd($email_content, 'verification_link');
        //dd($verification_email);
        $notification = new Notification;
        $notification->addNotification('user_register', $user->id, $user->id, 'User register', '');
        return response()->json(
                        [
                            "status" => 1,
                            "code" => 200,
                            "message" => "Thank you for registration.Verify your email address to get started",
                            'data' => $request->all()
                        ]
        );
    }

    public function createImage($base64) {
        try {
            $img = explode(',', $base64);
            if (is_array($img) && isset($img[1])) {
                $image = base64_decode($img[1]);
                $image_name = time() . '.jpg';
                $path = storage_path() . "/image/" . $image_name;
                file_put_contents($path, $image);
                return url::to(asset('storage/image/' . $image_name));
            } else {
                if (starts_with($base64, 'http')) {
                    return $base64;
                }
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function userDetail($id = null) {
        $user = User::find($id);
        return Response::json(array(
                    'status' => ($user) ? 1 : 0,
                    'code' => ($user) ? 200 : 404,
                    'message' => ($user) ? 'User data found.' : 'Record not found!',
                    'data' => $user
                        )
        );
    }

    public function updateKyc(Request $request, $userId) {
        $document_name = ['adharCard', 'panCard', 'voterId', 'drivingLicense'];
        $vendor = Vendor::findOrNew(['user_id', $userId]);
        if (in_array($request->get('documentName'), $document_name)) {
            $kyc = Kyc::findOrNew(['vendor_id', $vendor->id, 'document_name' => $request->get('documentName')]);
        } else {
            $kyc = Kyc::findOrNew(['vendor_id', $vendor->id]);
        }
        $kyc->document_name = $request->get('documentName');
        $kyc->document_type = $request->get('documentType');
        $kyc->vendor_id = $vendor->id;
        $kyc->is_verified = "No";
        $kyc->verified_by = "";
        $kyc->status = "Pending";
        return Response::json(array(
                    'status' => ($kyc) ? 1 : 0,
                    'code' => ($kyc) ? 200 : 404,
                    'message' => "Kyc updated and pending for review.",
                    'data' => []
                        )
        );
    }

    // vendor update
    public function vendorUpdate(Request $request, $userId) {
        $table_cname = \Schema::getColumnListing('vendors');
        $except = ['id', 'created_at', 'updated_at', 'shopType'];
        $vendor = Vendor::firstOrNew(['user_id' => $userId]);
        $userId = User::find($userId);
        if ($request->get('first_name') || $request->get('last_name')) {
            $vendor->vendor_name = $request->get('first_name') . ' ' . $request->get('last_name');
        }
        $vendor->type = $request->get('shopType');
        $vendor->role_type = $userId->role_type;
        if ($request->get('profileImage')) {
            $profile_image = $this->createImage($request->get('profileImage'));
            if ($profile_image == false) {
                
            } else {
                $vendor->profile_picture = $profile_image;
            }
        }
        if ($request->get('latitude')) {
            $vendor->lat = $request->get('latitude');
        }
        if ($request->get('longitude')) {
            $vendor->lng = $request->get('longitude');
        }
        foreach ($table_cname as $key => $value) {
            if (in_array($value, $except)) {
                continue;
            }
            if ($request->get(camel_case($value))) {
                $vendor->$value = $request->get(camel_case($value));
            }
        }
        try {
            $vendor->save();
            $status = 1;
            $code = 200;
            $message = "Vendor Profile updated successfully";
        } catch (\Exception $e) {
            $status = 0;
            $code = 201;
            $message = $e->getMessage();
        }
        return response()->json(
                        [
                            "status" => $status,
                            'code' => $code,
                            "message" => $message,
                            'data' => []
                        ]
        );
    }

    /* @method : update User Profile
     * @param : email,password,deviceID,firstName,lastName
     * Response : json
     * Return : token and user details
     * Author : kundan Roy
     * Calling Method : get
     */

    public function updateProfile(Request $request, $userId) {
        $user = User::find($userId);
        $role = Config::get('role');
        if ((User::find($userId)) == null) {
            return Response::json(array(
                        'status' => 0,
                        'code' => 201,
                        'message' => 'Invalid user Id!',
                        'data' => []
                            )
            );
        }
        $table_cname = \Schema::getColumnListing('users');
        $except = ['id', 'created_at', 'updated_at', 'profile_image', 'modeOfreach', 'email'];
        foreach ($table_cname as $key => $value) {
            if (in_array($value, $except)) {
                continue;
            }
            if ($request->get($value)) {
                $user->$value = $request->get($value);
            }
        }
        if ($request->get('profilePicture')) {
            $profile_image = $this->createImage($request->get('profilePicture'));
            if ($profile_image == false) {
                return Response::json(array(
                            'status' => 0,
                            'code' => 201,
                            'message' => 'Invalid Image format!',
                            'data' => $request->all()
                                )
                );
            }
            $user->profile_image = $profile_image;
        }
        try {
            $user->save();
            $status = 1;
            $code = 200;
            $message = "Profile updated successfully";
        } catch (\Exception $e) {
            $status = 0;
            $code = 201;
            $message = $e->getMessage();
        }
        return response()->json(
                        [
                            "status" => $status,
                            'code' => $code,
                            "message" => $message,
                            'data' => []
                        ]
        );
    }

    // Validate user
    public function validateInput($request, $input) {
        //Server side valiation
        $validator = Validator::make($request->all(), $input);
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 500,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
    }

    /* @method : login
     * @param : email,password and deviceID
     * Response : json
     * Return : token and user details
     * Author : kundan Roy
     */

    public function login(Request $request) {
        $input = $request->all();
        $user_type = $request->get('authType');
        // Validation
        $validateInput['email'] = 'required|email';
        $v = $this->validateInput($request, $validateInput);
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
		    'password' => 'required'
                           ]);
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        switch ($user_type) {
            case 'facebook':
                $token = Auth::attempt(['email' => $request->get('email'), 'provider_id' => $request->get('providerId')]);
                break;
            case 'google':
                $token = Auth::attempt(['email' => $request->get('email'), 'provider_id' => $request->get('providerId')]);
                break;
            case 'twitter':
                $token = Auth::attempt(['email' => $request->get('email'), 'provider_id' => $request->get('providerId')]);
                break;
            case 'linkedin':
                $token = Auth::attempt(['email' => $request->get('email'), 'provider_id' => $request->get('providerId')]);
                break;
            default:
                $token = Auth::attempt(
                                [
                                    'email' => $input['email'],
                                    'password' => $input['password'],
                ]);
                break;
        }
        
        /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        
        if (!$token) {
            return response()->json(["status" => 0, "code" => 201, "message" => "Invalid email or password!", 'data' => $input]);
        }
        $user = Auth()->user();
        try {
            return response()->json(["status" => 1, "code" => 200, "code" => 200, "message" => "Successfully logged in.", 'data' => $user]);
        } catch (DecryptException $e) {
            return response()->json(["status" => 0, "code" => 401, "message" => $e->getMessage(), 'data' => []]);
        }
    }

    /**
     * List of vendor products
     */
    public function getVendorProducts(Request $request) {

        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        
         /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        $customer_prodcuts = [];
        $stores = \Corals\Modules\Marketplace\Models\Store::where(array('user_id' => $input['user_id'], 'status' => 'active'))->get();
        $product_category = \Corals\Modules\Marketplace\Models\Category::where(array('status' => 'active'))->get();
        //$customer_prodcuts['productCategories'] = $product_category->toArray();
        if (isset($stores) && count($stores) > 0) {

            foreach ($stores as $key => $store) {
                $customer_prodcuts['stores'][$key] = $store->toArray();

                foreach ($product_category as $k => $pc) {

                    $customer_prodcuts['stores'][$key]['category'][$k] = $pc->toArray();
                    //$store_products = \Corals\Modules\Marketplace\Models\Product::where(array('store_id' => $store->id,'id' => $pc->id,'status' => 'active'))->get();
                    $store_products = $pc->products;
                    if (isset($store_products) && count($store_products) > 0) {

                        foreach ($store_products as $key1 => $store_product) {
                            
                            if($store_product->store_id == $store->id){

				$sku = \Corals\Modules\Marketplace\Models\SKU::where(array('product_id' => $store_product->id, 'status' => 'active'))->first();
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][] = array(
                                    
                                    'category' => $store_product->categories[0]->id,
                                    'productBrand' => isset($store_product->brand->name) ? $store_product->brand->name : null,
                                    'brandSlug' => isset($store_product->brand->slug) ? $store_product->brand->slug : null,
                                    'id' => $store_product->id,
                                    'name' => $store_product->name,
                                    'type' => $store_product->type,
                                    'description' => strip_tags($store_product->description),
                                    'brand_id' => $store_product->brand_id,
                                    'shipping' => $store_product->shipping,
                                    'store_id' => $store_product->store_id,
                                    'external_url' => $store_product->external_url,
                                    'product_image' => $store_product->getImageAttribute(),
                                    'sku' => array(
                                        'id' => $sku->id,
                                        'regular_price' => $sku->regular_price,
                                        'sale_price' => $sku->sale_price,
                                        'code' => $sku->code,
                                        'inventory' => $sku->inventory,
                                        'product_id' => $sku->product_id,
                                        'inventory_value' => $sku->inventory_value,
                                        'allowed_quantity' => $sku->allowed_quantity
                                    )
                                );                                
                                /*$customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['category'] = $store_product->categories[0]->id;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['productBrand'] = isset($store_product->brand->name) ? $store_product->brand->name : null;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['brandSlug'] = isset($store_product->brand->slug) ? $store_product->brand->slug : null;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['id'] = $store_product->id;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['name'] = $store_product->name;
                                $customer_prodcuts['stores'][$key]['category'][$k]['category'][$k]['products'][$key1]['type'] = $store_product->type;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['description'] = strip_tags($store_product->description);
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['brand_id'] = $store_product->brand_id;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['shipping'] = $store_product->shipping;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['store_id'] = $store_product->store_id;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['external_url'] = $store_product->external_url;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['product_image'] = $store_product->getImageAttribute();
                                $sku = \Corals\Modules\Marketplace\Models\SKU::where(array('product_id' => $store_product->id, 'status' => 'active'))->first();
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['sku']['id'] = $sku->id;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['sku']['regular_price'] = $sku->regular_price;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['sku']['sale_price'] = $sku->sale_price;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['sku']['code'] = $sku->code;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['sku']['inventory'] = $sku->inventory;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['sku']['product_id'] = $sku->product_id;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['sku']['inventory_value'] = $sku->inventory_value;
                                $customer_prodcuts['stores'][$key]['category'][$k]['products'][$key1]['sku']['allowed_quantity'] = $sku->allowed_quantity;*/
                            }
                        }
                    }
                }
            }
        }
        return response()->json(["status" => 1, "code" => 200, "message" => "", 'data' => $customer_prodcuts]);
    }

    public function updateProductPrice(Request $request) {

        $input = $request->all();
        $validator = Validator::make($request->all(), [
                    'product_id' => 'required',
                    'sku_id' => 'required',
                    'regular_price' => 'required',
                    'sale_price' => 'required'
        ]);

        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
         /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        $skuModel = \Corals\Modules\Marketplace\Models\SKU::where(array('id' => $input['sku_id'], 'product_id' => $input['product_id'], 'status' => 'active'))->first();
        if (isset($skuModel)) {

            $skuModel->regular_price = $input['regular_price'];
            $skuModel->sale_price = $input['sale_price'];
            $status = $skuModel->save();
        }
        if ($status) {

            return response()->json(["status" => 1, "code" => 200, "message" => "Data save successfully."]);
        } else {

            return response()->json(["status" => 0, "code" => 201, "message" => "No record found."]);
        }
    }

    public function getOrderHistory(Request $request) {

        $input = $request->all();
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'store_id' => 'required'
        ]);

        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
         /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        $orders = \Corals\Modules\Marketplace\Models\Order::where(array('store_id' => $input['store_id']))->get();
        $complete_order = [];
        if (isset($orders) && count($orders) > 0) {
            foreach ($orders as $k => $order) {
                $market_order = [];
                $market_order['order_number'] = $order->order_number;
                $market_order['order_id'] = $order->id;
                $market_order['amount'] = $order->amount;
                $market_order['currency'] = $order->currency;
                $market_order['shipping'] = $order->shipping;
                $market_order['billing'] = $order->billing;
                $market_order['store_id'] = $order->store_id;
                $market_order['created_time'] = $order->created_at;
                $market_order['updated_time'] = $order->updated_at;

                if ($order->status == 'completed') {

                    $complete_order['complete'][] = $market_order;
                } else if ($order->status == 'pending') {

                    $complete_order['pending'][] = $market_order;
                } else if ($order->status == 'processing') {

                    $complete_order['processing'][] = $market_order;
                } else if ($order->status == 'canceled') {

                    $complete_order['canceled'][] = $market_order;
                }
            }
        }

        return response()->json(["status" => 1, "code" => 200, "message" => "", 'data' => $complete_order]);
    }

    public function getOrderItems(Request $request) {

        $input = $request->all();
        $validator = Validator::make($request->all(), [
                    'order_id' => 'required'
        ]);

        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        
         /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        $orderItems = \Corals\Modules\Marketplace\Models\OrderItem::where(array('order_id' => $input['order_id']))->get();
        $categories = \Corals\Modules\Marketplace\Models\Category::where(array('status' => 'active'))->get();
        $order_items = $final_arr = [];
        if (isset($orderItems) && count($orderItems) > 0 && isset($categories)) {
            foreach ($orderItems as $key => $order_item) {

                if (isset($order_item->sku->product->categories[0]->name)) {

                    $order_items[$key]['cat_name'] = $order_item->sku->product->categories[0]->name;
                    $order_items[$key]['cat_id'] = $order_item->sku->product->categories[0]->id;
                    $order_items[$key]['products']['product_name'] = $order_item->sku->product->name;
                    $order_items[$key]['products']['product_img'] = $order_item->sku->product->getImageAttribute();
                    $order_items[$key]['products']['order_id'] = $order_item->order_id;
                    $order_items[$key]['products']['order_item_number'] = $order_item->id;
                    $order_items[$key]['products']['amount'] = $order_item->amount;
                    $order_items[$key]['products']['description'] = $order_item->description;
                    $order_items[$key]['products']['quantity'] = $order_item->quantity;
                    $order_items[$key]['products']['sku_code'] = $order_item->sku_code;
                    $order_items[$key]['products']['type'] = $order_item->type;
                    $order_items[$key]['products']['item_options'] = $order_item->item_options;
                    $order_items[$key]['products']['created_time'] = $order_item->created_at;
                    $order_items[$key]['products']['updated_time'] = $order_item->updated_at;
                }
            }
            $final_arr = [];
            foreach ($categories as $k => $cat) {
                $cat_arr = [];
                foreach ($order_items as $key => $orderItm) {

                    if ($orderItm['cat_id'] == $cat->id) {

                        $cat_arr['category']['category_id'] = $cat->id;
                        $cat_arr['category']['category_name'] = $cat->name;
                        $cat_arr['products'][] = $orderItm['products'];
                    }
                }
                if (count($cat_arr) > 0) {
                    $final_arr['order_details'][] = $cat_arr;
                }
            }
            $total_items = $total_amount = 0;
            foreach ($orderItems as $ot) {
                $total_items = $total_items + $ot->quantity;
                $total_amount = $total_amount + $ot->amount;
            }
            $final_arr['total_items'] = $total_items;
            $final_arr['total_amt'] = $total_amount;
        }

        return response()->json(["status" => 1, "code" => 200, "message" => "", 'data' => $final_arr]);
    }

    public function orderAction(Request $request) {

        $input = $request->all();
        $validator = Validator::make($request->all(), [
                    'order_id' => 'required',
                    'order_status' => 'required'
        ]);

        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        $order = \Corals\Modules\Marketplace\Models\Order::where(array('id' => $input['order_id']))->first();
        if ($order) {

            $order->status = $input['order_status'];
            $order->save();
            return response()->json(["status" => 1, "code" => 200, "message" => "Order status updated successfully."]);
        }

        return array(
            'status' => 0,
            'code' => 201,
            'message' => "Order not found",
            'data' => $request->all()
        );
    }

    /* @method : get user details
     * @param : Token and deviceID
     * Response : json
     * Return : User details
     */

    public function getUserDetails(Request $request) {
        $user = JWTAuth::toUser($request->input('token'));
        return response()->json(
                        ["status" => 1,
                            "code" => 200,
                            "message" => "success.",
                            "data" => $user
                        ]
        );
    }

    /* @method : Email Verification
     * @param : token_id
     * Response : json
     * Return :token and email
     */

    public function emailVerification(Request $request) {
        $verification_code = ($request->input('verification_code'));
        $email = ($request->input('email'));
        if (Hash::check($email, $verification_code)) {
            $user = User::where('email', $email)->get()->count();
            if ($user > 0) {
                User::where('email', $email)->update(['status' => 1]);
            } else {
                echo "Verification link is Invalid or expire!";
                exit();
                return response()->json(["status" => 0, "message" => "Verification link is Invalid!", 'data' => '']);
            }
            echo "Email verified successfully.";
            exit();
            return response()->json(["status" => 1, "message" => "Email verified successfully.", 'data' => '']);
        } else {
            echo "Verification link is Invalid!";
            exit();
            return response()->json(["status" => 0, "message" => "Verification link is invalid!", 'data' => '']);
        }
    }

    /* @method : logout
     * @param : token
     * Response : "logout message"
     * Return : json response
     */

    public function logout(Request $request) {
        $token = $request->input('token');
        JWTAuth::invalidate($request->input('token'));
        return response()->json([
                    "status" => 1,
                    "code" => 200,
                    "message" => "You've successfully signed out.",
                    'data' => []
                        ]
        );
    }

    /* @method : forget password
     * @param : token,email
     * Response : json
     * Return : json response
     */

    public function forgetPassword(Request $request) {
        $email = $request->input('email');
        //Server side valiation
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email'
        ]);
        $helper = new Helper;
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'message' => $error_msg[0],
                        'data' => ''
                            )
            );
        }
        $user = User::where('email', $email)->first();
        if ($user == null) {
            return Response::json(array(
                        'status' => 0,
                        'code' => 201,
                        'message' => "Oh no! The address you provided isn't in our system",
                        'data' => $request->all()
                            )
            );
        }
        $user_data = User::find($user->id);
        $temp_password = Hash::make($email);
        // Send Mail after forget password
        $temp_password = Hash::make($email);
        $email_content = array(
            'receipent_email' => $request->input('email'),
            'subject' => 'Your Ventrega Account Password',
            'first_name' => $user->first_name,
            'temp_password' => $temp_password,
            'encrypt_key' => Crypt::encrypt($email),
            'greeting' => 'Ventrega'
        );
        $helper = new Helper;
        $email_response = $helper->sendMail(
                $email_content, 'forgot_password_link'
        );
        return response()->json(
                        [
                            "status" => 1,
                            "code" => 200,
                            "message" => "Reset password link has sent. Please check your email.",
                            'data' => $request->all()
                        ]
        );
    }

    public function resetPassword(Request $request) {
        $encryptedValue = $request->get('key') ? $request->get('key') : '';
        $method_name = $request->method();
        $token = $request->get('token');
        // $email = ($request->get('email'))?$request->get('email'):'';
        if ($method_name == 'GET') {
            try {
                $email = Crypt::decrypt($encryptedValue);
                if (Hash::check($email, $token)) {
                    return view('admin.auth.passwords.reset', compact('token', 'email'));
                } else {
                    return Response::json(array(
                                'status' => 0,
                                'message' => "Invalid reset password link!",
                                'data' => ''
                                    )
                    );
                }
            } catch (DecryptException $e) {
                //   return view('admin.auth.passwords.reset',compact('token','email'))
                //              ->withErrors(['message'=>'Invalid reset password link!']);
                return Response::json(array(
                            'status' => 0,
                            'message' => "Invalid reset password link!",
                            'data' => ''
                                )
                );
            }
        } else {
            try {
                $email = Crypt::decrypt($encryptedValue);
                if (Hash::check($email, $token)) {
                    $password = Hash::make($request->get('password'));
                    $user = User::where('email', $email)->update(['password' => $password]);
                    return Response::json(array(
                                'status' => 1,
                                'message' => "Password reset successfully.",
                                'data' => []
                                    )
                    );
                } else {
                    return Response::json(array(
                                'status' => 0,
                                'message' => "Invalid reset password link!",
                                'data' => ''
                                    )
                    );
                }
            } catch (DecryptException $e) {
                return Response::json(array(
                            'status' => 0,
                            'message' => "Invalid reset password link!",
                            'data' => []
                                )
                );
            }
        }
    }

    /* @method : change password
     * @param : token,oldpassword, newpassword
     * Response : "message"
     * Return : json response
     */

    public function changePassword(Request $request) {
        $email = $request->input('email');
        //Server side valiation
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email'
        ]);
        $helper = new Helper;
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        "code" => 201,
                        'message' => $error_msg[0],
                        'data' => $request->all()
                            )
            );
        }
        $user = User::where('email', $email)->first();
        if ($user == null) {
            return Response::json(array(
                        'status' => 0,
                        'code' => 500,
                        'message' => "The email address you provided isn't in our system",
                        'data' => $request->all()
                            )
            );
        }
        $user = User::where('email', $request->get('email'))->first();
        $user_id = $user->id;
        $old_password = $user->password;
        $validator = Validator::make($request->all(), [
                    'oldPassword' => 'required',
                    'newPassword' => 'required|min:6'
        ]);
        // Return Error Message
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'message' => $error_msg[0],
                        'data' => ''
                            )
            );
        }
        if (Hash::check($request->input('oldPassword'), $old_password)) {
            $user_data = User::find($user_id);
            $user_data->password = Hash::make($request->input('newPassword'));
            $user_data->save();
            return response()->json([
                        "status" => 1,
                        "code" => 200,
                        "message" => "Password changed successfully.",
                        'data' => []
                            ]
            );
        } else {
            return Response::json(array(
                        'status' => 0,
                        "code" => 500,
                        'message' => "Old password mismatch!",
                        'data' => []
                            )
            );
        }
    }

    /* SORTING */

    public function array_msort($array, $cols) {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k]))
                    $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    }

    public function InviteUser(Request $request, InviteUser $inviteUser) {
        $user = $inviteUser->fill($request->all());
        $user_id = $request->input('userID');
        $invited_user = User::find($user_id);
        $user_first_name = $invited_user->first_name;
        $download_link = "http://google.com";
        $user_email = $request->input('email');
        $helper = new Helper;
        $cUrl = $helper->getCompanyUrl($user_email);
        $user->company_url = $cUrl;
        /** --Send Mail after Sign Up-- * */
        $user_data = User::find($user_id);
        $sender_name = $user_data->first_name;
        $invited_by = $invited_user->first_name . ' ' . $invited_user->last_name;
        $receipent_name = "User";
        $subject = ucfirst($sender_name) . " has invited you to join";
        $email_content = array('receipent_email' => $user_email, 'subject' => $subject, 'name' => 'User', 'invite_by' => $invited_by, 'receipent_name' => ucwords($receipent_name));
        $helper = new Helper;
        $invite_notification_mail = $helper->sendNotificationMail($email_content, 'invite_notification_mail', ['name' => 'User']);
        $user->save();
        return response()->json([
                    "status" => 1,
                    "code" => 200,
                    "message" => "You've invited your colleague, nice work!",
                    'data' => ['receipentEmail' => $user_email]
                        ]
        );
    }

    public function cDashboard() {
        // $cd = CategoryDashboard::all
        $image_url = env('IMAGE_URL', url::asset('storage/uploads/category/'));
        $categoryDashboard = CategoryDashboard::with('category')->limit(8)->get();
        $data = [];
        $category_data = [];
        foreach ($categoryDashboard as $key => $value) {
            if (isset($value->category)) {
                $data['category_id'] = $value->category->id;
                $data['category_name'] = $value->category->category_name;
                $data['category_image'] = $image_url . '/' . $value->category->category_image;
                $data['group_id'] = $value->category->parent->id;
                $data['category_group_name'] = $value->category->parent->category_group_name;
                $data['category_group_image'] = $image_url . '/' . $value->category->category_group_image;
                $category_data[] = $data;
            }
        }
        if (count($data)) {
            $status = 1;
            $code = 200;
            $msg = "Category dashboard list";
        } else {
            $status = 0;
            $code = 404;
            $msg = "Category dashboard list not  found!";
        }
        return response()->json([
                    "status" => $status,
                    "code" => $code,
                    "message" => $msg,
                    'data' => $category_data
                        ]
        );
    }

    public function groupCategory(Request $request) {
        $image_url = env('IMAGE_URL', url::asset('storage/uploads/category/'));
        $catId = null;
        $arr = [];
        try {
            $categoryDashboard = Category::with(['groupCategory' => function($q) {
                            $q->select('id', 'category_name', 'category_image', 'description', 'parent_id');
                        }])->where('parent_id', '=', 0)->get();
            $data = [];
            foreach ($categoryDashboard as $key => $value) {
                $data['group_id'] = $value->id;
                $data['category_group_name'] = $value->category_group_name;
                $data['category_group_image'] = $image_url . '/' . $value->category_group_image;
                $data['category'] = isset($value->groupCategory) ? $value->groupCategory : [];
                $arr[] = $data;
            }
        } catch (\Exception $e) {
            $data = [];
            $status = 0;
            $code = 500;
            $msg = $e->getMessage();
        }
        if (count($data)) {
            $status = 1;
            $code = 200;
            $msg = "Category list found";
        } else {
            $status = 0;
            $code = 404;
            $msg = "Record not  found!";
        }
        return response()->json([
                    "status" => $status,
                    "code" => $code,
                    "message" => $msg,
                    'data' => $arr
                        ]
        );
    }

    public function allCategory(Request $request) {
        $image_url = env('IMAGE_URL', url::asset('storage/uploads/category/'));
        $catId = null;
        $arr = [];
        try {
            $categoryDashboard = Category::where('parent_id', '!=', 0)->get();
            $data = [];
            foreach ($categoryDashboard as $key => $value) {
                $data['category_id'] = $value->id;
                $data['category_name'] = $value->category_name;
                $data['category_image'] = $image_url . '/' . $value->category_image;
                $arr[] = $data;
            }
        } catch (\Exception $e) {
            $data = [];
            $status = 0;
            $code = 500;
            $msg = $e->getMessage();
        }
        if (count($data)) {
            $status = 1;
            $code = 200;
            $msg = "Category list found";
        } else {
            $status = 0;
            $code = 404;
            $msg = "Record not  found!";
        }
        return response()->json([
                    "status" => $status,
                    "code" => $code,
                    "message" => $msg,
                    'data' => $arr
                        ]
        );
    }

    public function otherCategory(Request $request) {
        $image_url = env('IMAGE_URL', url::asset('storage/uploads/category/'));
        $catId = null;
        if ($request->get('categoryId')) {
            $catId = $request->get('categoryId');
            $category = Category::where('id', $catId)->first();
            $name = 'otherCategory';
            $id = $category->parent_id;
        }
        if ($request->get('groupId')) {
            $catId = $request->get('groupId');
            $category = Category::where('id', $catId)->first();
            $id = $category->id;
            $name = 'groupCategory';
        }
        try {
            $categoryDashboard = Category::where('parent_id', $id)->where('id', '!=', $catId)->where('parent_id', '!=', 0)->get();
            //  $categoryDashboard = Category::where('parent_id',$id)->get();
            $data = [];
            $data['category_id'] = $category->id;
            $data['group_id'] = ($category->parent_id == 0) ? $category->id : $category->parent_id;
            $data['category_group_name'] = $category->category_group_name;
            $data['category_group_image'] = $image_url . '/' . $category->category_group_image;
            $data[$name] = $categoryDashboard;
        } catch (\Exception $e) {
            $data = [];
            $status = 0;
            $code = 500;
            $msg = "Id does not exist";
        }
        if (count($data)) {
            $status = 1;
            $code = 200;
            $msg = "Category of other Category list";
        } else {
            $status = 0;
            $code = 404;
            $msg = "Record not  found!";
        }
        return response()->json([
                    "status" => $status,
                    "code" => $code,
                    "message" => $msg,
                    'data' => $data
                        ]
        );
    }

    public function category() {
        // $cd = CategoryDashboard::all
        $image_url = env('IMAGE_URL', url::asset('storage/uploads/category/'));
        $categoryDashboard = Category::with('children')->where('parent_id', 0)->get();
        $data = [];
        $category_data = [];
        foreach ($categoryDashboard as $key => $value) {
            $data['group_id'] = $value->id;
            $data['category_group_name'] = $value->category_group_name;
            $data['category_group_image'] = $image_url . '/' . $value->category_group_image;
            foreach ($value->children as $key => $result) {
                $data2['category_id'] = $result->id;
                $data2['category_name'] = $result->category_name;
                $data2['category_image'] = $image_url . '/' . $result->category_image;
                $data2['category_group_id'] = $result->parent_id;
                $data2['category_group_name'] = $value->category_group_name;
                $data2['description'] = $result->description;
                $data['category'][] = $data2;
            }
            $category_data[] = $data;
        }
        if (count($data)) {
            $status = 1;
            $code = 200;
            $msg = "Category dashboard list";
        } else {
            $status = 0;
            $code = 404;
            $msg = "Category dashboard list not  found!";
        }
        return response()->json([
                    "status" => $status,
                    "code" => $code,
                    "message" => $msg,
                    'data' => $category_data
                        ]
        );
    }

    public function sendMail() {
        $emails = ['kroy@mailinator.com'];
        Mail::send('emails.welcome', [], function($message) use ($emails) {
            $message->to($emails)->subject('This is test e-mail');
        });
        var_dump(Mail:: failures());
        exit;
    }

    //array_msort($array, $cols)
    public function addPersonalMessage(Request $request) {
        $rs = $request->all();
        $validator = Validator::make($request->all(), [
                    'taskId' => "required",
                    'userId' => "required",
                    'comments' => "required"
        ]);
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'code' => 500,
                        'message' => $error_msg[0],
                        'data' => $request->all()
                            )
            );
        }
        $input = [];
        foreach ($rs as $key => $val) {
            $input[$key] = $val;
        }
        \DB::table('messges')->insert($input);
        return response()->json(
                        [
                            "status" => 1,
                            'code' => 200,
                            "message" => "Message added successfully.",
                            'data' => $input
                        ]
        );
    }

    public function getPersonalMessage(Request $request) {
        $rs = $request->all();
        $validator = Validator::make($request->all(), [
                    'taskId' => "required",
                        // 'poster_userid' => "required"
        ]);
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'code' => 500,
                        'message' => $error_msg[0],
                        'data' => $request->all()
                            )
            );
        }
        $posteduserid = $request->get('postedUserId');
        $doerUserid = $request->get('doerUserid');
        $data = Messges::with('commentPostedUser')
                        ->with(['taskDetails' => function($q)use($posteduserid, $doerUserid, $request) {
                                if ($doerUserid) {
                                    $q->where('taskDoerId', $doerUserid);
                                }if ($posteduserid) {
                                    $q->where('taskOwnerId', $posteduserid);
                                }
                            }])
                        ->where('taskId', $request->get('taskId'))
                        ->where(function($q)use($posteduserid, $doerUserid, $request) {
                            if ($posteduserid) {
                                $q->where('userId', $posteduserid);
                            }
                            if ($doerUserid) {
                                $q->where('userId', $doerUserid);
                            }
                        })->get();
        return response()->json(
                        [
                            "status" => count($data) ? 1 : 0,
                            'code' => count($data) ? 200 : 404,
                            "message" => count($data) ? "Message found" : "Message not found",
                            'data' => $data
                        ]
        );
    }

    public function generateOtp(Request $request) {
        $rs = $request->all();
        $validator = Validator::make($request->all(), [
                    'userId' => "required",
                    'mobileNumber' => 'required'
        ]);
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'code' => 500,
                        'message' => $error_msg[0],
                        'data' => $request->all()
                            )
            );
        }
        $otp = mt_rand(100000, 999999);
        $data['otp'] = $otp;
        $data['userId'] = $request->get('userId');
        $data['timezone'] = config('app.timezone');
        $data['mobile'] = $request->get('mobileNumber');
        \DB::table('mobile_otp')->insert($data);
        $this->sendSMS($request->get('mobileNumber'), $otp);
        return response()->json(
                        [
                            "status" => count($data) ? 1 : 0,
                            'code' => count($data) ? 200 : 401,
                            "message" => count($data) ? "Otp generated" : "Something went wrong",
                            'data' => $data
                        ]
        );
    }

    public function verifyOtp(Request $request) {
        $rs = $request->all();
        $validator = Validator::make($request->all(), [
                    'otp' => "required",
                    'userId' => 'required'
        ]);
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'code' => 500,
                        'message' => $error_msg[0],
                        'data' => $request->all()
                            )
            );
        }
        $data = \DB::table('mobile_otp')
                        ->where('otp', $request->get('otp'))
                        ->where('userId', $request->get('userId'))->first();
        if ($data) {
            \DB::table('mobile_otp')
                    ->where('otp', $request->get('otp'))
                    ->where('userId', $request->get('userId'))->update(['is_verified' => 1]);
            \DB::table('users')
                    ->where('id', $request->get('userId'))
                    ->update(['phone' => $data->mobile]);
        }
        return response()->json(
                        [
                            "status" => count($data) ? 1 : 0,
                            'code' => count($data) ? 200 : 500,
                            "message" => count($data) ? "Otp Verified" : "Invalid Otp",
                            'data' => $request->all()
                        ]
        );
    }

    public function sendSMS($mobileNumber = null, $otp = null) {
        $curl = curl_init();
        $modelNumber = $mobileNumber;
        $message = "Your verification OTP is : " . $otp;
        $authkey = "224749Am2kvmYg75b4092ed";
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://control.msg91.com/api/sendotp.php?template=&otp_length=6&authkey=$authkey&message=$message&sender=YTASKR&mobile=$modelNumber&otp=$otp&otp_expiry=&email=kroy@mailinator.com",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return false;
        } else {
            return true;
        }
    }

    public function getCategoryById($id) {
        $url = Category::where('id', $id)->first();
        return $url->slug . '/';
    }

    public function AddVendorProduct(Request $request) {
        $validator = Validator::make($request->all(), [
                    'productTitle' => 'required',
                    'storePrice' => 'required',
                    'productCategory' => 'required',
                    'photo' => 'mimes:jpeg,bmp,png,gif,jpg,PNG',
        ]);
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'code' => 201,
                        'message' => $error_msg[0],
                        'data' => $request->all()
                            )
            );
        }
        $cat_url = $this->getCategoryById($request->get('productCategory'));
        $pro_slug = str_slug($request->get('productTitle'));
        $url = $cat_url . $pro_slug;
        $product = new Product;
        $product->slug = $pro_slug;
        $product->url = $url;
        try {
            \DB::beginTransaction();
            $table_cname = \Schema::getColumnListing('products');
            $except = ['id', 'created_at', 'updated_at', 'deleted_at', 'additional_images', 'btn_name'];
            foreach ($table_cname as $key => $value) {
                if (in_array($value, $except)) {
                    continue;
                }
                if ($request->get(camel_case($value))) {
                    $product->$value = $request->get(camel_case($value));
                }
            }
            $product->save();
            // vendor
            $vendorProduct = VendorProduct::firstOrNew(
                            [
                                'vendor_id' => $request->get('vendorId'),
                                'product_id' => $product->id
                            ]
            );
            $vendorProduct->vendor_id = $request->get('vendorId');
            $vendorProduct->product_id = $product->id;
            $vendorProduct->save();
            \DB::commit();
            $msg = 'New Product was successfully created !';
        } catch (\Exception $e) {
            \DB::rollback();
            $msg = $e->getMessage();
        }
        return response()->json(
                        [
                            "status" => 1,
                            "code" => 200,
                            "message" => $msg,
                            'data' => $request->all()
                        ]
        );
    }

    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), [
                    'product_id' => 'required',
                    'vendor_id' => 'required'
        ]);
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            return Response::json(array(
                        'status' => 0,
                        'code' => 201,
                        'message' => $error_msg[0],
                        'data' => $request->all()
                            )
            );
        }
        $product = Product::findOrFail($request->get('product_id'))->delete();
        $msg = 'Product was successfully deleted !';
        return response()->json(
                        [
                            "status" => 1,
                            "code" => 200,
                            "message" => $msg,
                            'data' => $request->all()
                        ]
        );
    }

    public function getProductUnit() {
        $productunits = ProductUnit::where('status', 1)->pluck('id', 'name');
        if (count($productunits)) {
            $status = 1;
            $code = 200;
            $msg = "Product Unit list.";
        } else {
            $status = 0;
            $code = 404;
            $msg = "Product Unit list not found!";
        }
        return response()->json([
                    "status" => $status,
                    "message" => $msg,
                    'data' => $productunits
                        ]
        );
    }

    public function getProductType() {
        $producttypes = ProductType::where('status', 1)->pluck('id', 'name');
        if (count($producttypes)) {
            $status = 1;
            $code = 200;
            $msg = "Product Type list.";
        } else {
            $status = 0;
            $code = 404;
            $msg = "Product Type list not found!";
        }
        return response()->json([
                    "status" => $status,
                    "message" => $msg,
                    'data' => $producttypes
                        ]
        );
    }

    public function customerLogin(Request $request) {
        
        $input = $request->all();
        $user_type = $request->get('authType');
        // Validation
        $validateInput['email'] = 'required|email';
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        
         /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        try {
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        $usermodel = \Corals\User\Models\User::where(function ($query) use($input) { $query->where(array('email' => $input['email']));})->first();
        $otp = mt_rand(1000, 9999);
        if($usermodel){
            
            $usermodel->otp = $otp;
            $usermodel->save();
            $email_data = array('otp' => $otp,'first_name' => $usermodel->name,'email' => $input['email']);
            $d = \Illuminate\Support\Facades\Mail::send('emails.verifyOTP', $email_data , function ($message) use ($email_data) {
                $message->subject('Verify OTP')
                    ->from('helpdeliverdas@gmail.com', 'Deliverdas.com')
                    ->to(strtolower($email_data['email']));
            });

	    $user_input['firstName'] = $usermodel->name;
            $user_input['email'] = $usermodel->email;
            $user_input['lastName'] = $usermodel->last_name;
            $user_input['mobileNumber'] = $usermodel->phone_number;
            $user_input['userId'] = $usermodel->id;
            $user_input['otp'] = $otp;
            return response()->json(["status" => 1, "code" => 200, "message" => "Successfully logged in.", 'data' => $user_input ]);
            
        }else{
            
            return response()->json(["status" => 0, "code" => 401, "message" => "User doesn't exsist.", 'data' => $input]);  
            
        }
        } catch (\Exception $e) {
            
            return response()->json(["status" => 0, "code" => 500, "message" => $e->getMessage()." ".$e->getLine(), 'data' => $input ]);
        }
    }
    
    public function cutomerSendOTP(Request $request) {
        
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'email' => 'required|email|regex:/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})/|email_check',
            'lastName' => 'required',
            'mobile' => 'required|numeric|regex:/(?!(0))[0-9]{10}/|mobile_check'
        ]);
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }

        try {
            //generate otp
            $otp = mt_rand(1000, 9999);
            //$sms_msg = "Use $otp as a verification code to verify your Nielsen Beta account.";
            //$sms_class = new \App\helper\SendSMS($input['mobile'], $sms_msg);
            $response = "OK sms gone.";//$sms_class->sendSMS();
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip_address = $_SERVER['REMOTE_ADDR'];
            }
            $data = array(
                'email_id' => strtolower($input['email']),
                'mobile_no' => $input['mobile'],
                'ip_address' => $ip_address,
                'user_agent' => $request->server('HTTP_USER_AGENT'),
                'otp' => $otp,
                'fname' => $input['firstName'],
                'lname' => $input['lastName'],
                'otp_status' => false,
                'sms_status' => $response,
                'http_referer' => $request->server('HTTP_REFERER')
            );

            $en_email = strtolower($input['email']);
            $en_mobile = $input['mobile'];
            
             /* store user latest hardware info */
            $hardwareInfo = new \App\HardwareInfo();
            $hardwareInfo->data = $input['hardware_info'];
            $hardwareInfo->Mysave();
            
            $email_data = array('otp' => $otp,'first_name' => $input['firstName'],'email' => $input['email']);
            $d = \Illuminate\Support\Facades\Mail::send('emails.verifyOTP', $email_data  , function ($message) use($email_data) {
                $message->subject('Verify OTP')
                    ->from('helpdeliverdas@gmail.com', 'Deliverdas.com')
                    ->to(strtolower($email_data['email']));
            });
            
            $otpModel = \App\send_otp::where(array('email_id' => $en_email, 'mobile_no' => $en_mobile, 'otp_status' => 0))->first();
            if (isset($otpModel) && isset($otpModel->id)) {
                $otpModel->fill($data);
                $otpModel->save();
            } else {
                \App\send_otp::Create($data);
            }
	    $input['otp'] = $otp;

            return response()->json(["status" => 1, "code" => 200, "message" => "OTP sent successfully on your register mobile number.", 'data' => $input]);
            
        } catch (\Exception $e) {

            $errors = $e->getMessage();
        }
        return response()->json(["status" => 0, "code" => 500, "message" => $errors, 'data' => $input]);
    }
    
    public function customerRegistration(Request $request){
        
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'email' => 'required|email|regex:/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})/',
            'lastName' => 'required',
            'mobile' => 'required_if:actionType,registration',
            'otp' => 'required',
            'actionType' => 'required'
        ]);
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }

        $en_email = strtolower($input['email']);
        $en_mobile = $input['mobile'];
        
         /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();

        if(isset($input['actionType']) && $input['actionType'] == 'registration') {

            $otpModel = \App\send_otp::where(array('email_id' => $en_email, 'mobile_no' => $en_mobile))->first();

            if (isset($otpModel->otp) && $otpModel->otp != null) {

                if ($otpModel->otp == $input['otp']) {

                    //update otp table 
                    $otpModel->otp_status = true;
                    $otpModel->save();
                    $pwd = \App\Helpers\Helper::generateRandomString(8);
                    $data = array(
                        'email' => strtolower($input['email']),
                        'phone_number' => $input['mobile'],
                        'password' => $pwd,
                        'remember_token' => uniqid("salted"),
                        'name' => $input['firstName'],
                        'last_name' => $input['lastName'],
                        'phone_country_code' => 91,
                        'job_title' => 'member'
                    );

                    $saveData = \Corals\User\Models\User::create($data);

                    \App\HasRole::create(array('role_id' => 2, 'model_type' => 'Corals\User\Models\User', 'model_id' => $saveData->id));
		     $input['userId'] = $saveData->id;
		     $input['user_id'] = $saveData->id;
                    /* store user latest hardware info */
                    $hardwareInfo = new \App\HardwareInfo();
                    $hardwareInfo->data = $input['hardware_info'];
                    $hardwareInfo->Mysave();
                    return response()->json(["status" => 1, "code" => 200, "message" => "Otp verified successfuly.", 'data' => $input]);
                } else {

                    return response()->json(["status" => 0, "code" => 201, "message" => "please enter valid otp.", 'data' => $input]);
                }
            }
        } elseif(isset($input['actionType']) && $input['actionType'] == 'login') {
            
            $usermodel = \Corals\User\Models\User::where(function ($query) use($input) { $query->where(array('email' => $input['email']));})->first();
            if($usermodel->otp == $input['otp']){
                
                $token = JWTAuth::fromUser($usermodel);
                if($token){
                    
                    $user_input['token'] = $token;
                    $user_input['firstName'] = $usermodel->name;
                    $user_input['email'] = $usermodel->email;
                    $user_input['lastName'] = $usermodel->last_name;
                    $user_input['mobileNumber'] = $usermodel->phone_number;
                    $user_input['userId'] = $usermodel->id;
		    $hardwareInfo = new \App\HardwareInfo();
                    $hardwareInfo->data = $input['hardware_info'];
                    $hardwareInfo->Mysave();
                    return response()->json(["status" => 1, "code" => 200, "message" => "Otp verified successfuly.", 'data' => $user_input]);                    
                }else{
                    
                   return response()->json(["status" => 0, "code" => 201, "message" => "Invalid credential.", 'data' => $input]); 
                }
            }else{
                
                return response()->json(["status" => 0, "code" => 201, "message" => "Please use valid OTP", 'data' => $input]);
            }
        }
        return response()->json(["status" => 0, "code" => 500, "message" => "Something went wrong.", 'data' => $input]);        
        
    }
    
    
    public function customerforgotPassword(Request $request){
        
        $input = $request->all();
        $user_type = $request->get('authType');
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        
        $usemodel = \Corals\User\Models\User::where(array('email' => $input['email']))->first();
        if (isset($usemodel) && $usemodel->id) {

            $pwd = \App\Helpers\Helper::generateRandomString(8);
            $userPWD = $pwd;
            $usemodel->password = $userPWD;
            $usemodel->save();
            /*$d = \Illuminate\Support\Facades\Mail::send('emails.welcome', array() , function ($message) {
                $message->subject('Verify OTP')
                    ->from('support@deliverdas.com', 'Deliverdas.com')
                    ->to("piyush071988@gmail.com");
            });*/
            return response()->json(["status" => 1, "code" => 200, "message" => "Password sent successfully on your email id.".$pwd, 'data' => $usemodel]);
         
        } 
        return response()->json(["status" => 0, "code" => 201, "message" => "User data not found", 'data' => $input]);
        
    }
    
    
    public function getStoreProduct(Request $request){
        
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'store_id' => 'required'
            
        ]);
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
         /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        $customer_cat_prodcuts = [];
        $storeProductss = \Corals\Modules\Marketplace\Models\product::where(array('store_id' => $input['store_id'], 'status' => 'active'));


 
        $page_number = ($request->get('page_number'))?$request->get('page_number'):1;
        $page_size = ($request->get('page_size'))?$request->get('page_size'):10; 
       

        if($page_number>1){
                  $offset = $page_size*($page_number-1);
            }else{
                  $offset = 0;
        }  
        $storeProducts =  $storeProductss->skip($offset)->take($page_size)->get(); 




        $product_categories = [];
	
        foreach($storeProducts as $proCat){
		
		foreach($proCat->activeCategories as $category) {
			$product_categories[] = array('id' => $category->id,'description' => $category->description,'name' => $category->name);	
		}
         }
	 $product_categories = array_map("unserialize", array_unique(array_map("serialize",$product_categories)));

         /*if (isset($storeProducts) && count($storeProducts) > 0 && isset($product_categories) && count($product_categories) > 0) {
            foreach ($product_categories as $k => $pc) {
               $customer_prodcuts['stores']['category'][$k] = array('id' => $pc['id'],'name' => $pc['name'], 'description' => $pc['description']);
                foreach ($storeProducts as $key1 => $store_product) {
      			
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['category'] = $pc['id'];
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['productBrand'] = isset($store_product->brand->name) ? $store_product->brand->name : null;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['brandSlug'] = isset($store_product->brand->slug) ? $store_product->brand->slug : null;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['id'] = $store_product->id;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['name'] = $store_product->name;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['type'] = $store_product->type;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['description'] = strip_tags($store_product->description);
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['brand_id'] = $store_product->brand_id;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['shipping'] = $store_product->shipping;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['store_id'] = $store_product->store_id;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['external_url'] = $store_product->external_url;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['product_image'] = $store_product->getImageAttribute();
                        $sku = \Corals\Modules\Marketplace\Models\SKU::where(array('product_id' => $store_product->id, 'status' => 'active'))->first();
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['sku']['id'] = $sku->id;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['sku']['regular_price'] = $sku->regular_price;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['sku']['sale_price'] = $sku->sale_price;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['sku']['code'] = $sku->code;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['sku']['inventory'] = $sku->inventory;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['sku']['product_id'] = $sku->product_id;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['sku']['inventory_value'] = $sku->inventory_value;
                        $customer_prodcuts['stores']['category'][$k]['products'][$key1]['sku']['allowed_quantity'] = $sku->allowed_quantity;
                    }
                }*/
		if (isset($storeProducts) && count($storeProducts) > 0 && isset($product_categories) && count($product_categories) > 0) {
            foreach ($product_categories as $k => $pc) {
                $customer_prodcuts = [];
                foreach ($storeProducts as $key1 => $store_product) {
			
      			if($store_product->categories[0]->id == $pc['id']){
                            $sku = \Corals\Modules\Marketplace\Models\SKU::where(array('product_id' => $store_product->id, 'status' => 'active'))->first();
                            $customer_prodcuts[] = array('category' => $pc['id'],
                                    'productBrand'=>(isset($store_product->brand->name) ? $store_product->brand->name : null),
                                    'brandSlug' =>isset($store_product->brand->slug) ? $store_product->brand->slug : null ,
                                    'id' => $store_product->id,
                                    'name' => $store_product->name,
                                    'type' => $store_product->type,
                                    'description' => strip_tags($store_product->description),
                                    'brand_id' => $store_product->brand_id,
                                    'shipping' => $store_product->shipping,
                                    'store_id' => $store_product->store_id,
                                    'external_url' => $store_product->external_url,
                                    'product_image' => $store_product->getImageAttribute(),
			            'product_attributes_label' => isset($store_product->attributes[0]->label) ? $store_product->attributes[0]->label : null,
                                    'product_attributes_dsp_order' => isset($store_product->attributes[0]->display_order) ? $store_product->attributes[0]->display_order : null,
                                    'sku' => array('id'=>$sku->id,'regular_price' => $sku->regular_price,'sale_price'=> $sku->sale_price,'code' => $sku->code,
                                            'inventory' => $sku->inventory,'product_id' => $sku->product_id, 'inventory_value' => $sku->inventory_value,
                                            'allowed_quantity' => $sku->allowed_quantity
                                        ));
                        }                    }
                    $customer_cat_prodcuts['stores']['category'][] = array('id' => $pc['id'],'name' => $pc['name'], 'description' => $pc['description'],'products'=> $customer_prodcuts);
                }
            }

		return response()->json(["status" => 1, "code" => 200, "message" => "", 'data' => $customer_cat_prodcuts]);        
    }
    
    

    public function customerChangePassword(Request $request) {
        
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'oldPassword' => 'required',
            'newPassword' => 'required|min:8|max:64|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&^()])[A-Za-z\d$@$!%*#?&^()]{8,64}/|different:oldPassword',
            'confPassword' => 'required|same:newPassword'
        ]);
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
         /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        if(Auth::attempt(array('email' => $input['email'], 'password' => $input['oldPassword']))){

            $userModel = \Illuminate\Support\Facades\Auth::user();
            $userModel->password = $input['newPassword'];
            $userModel->save();
            return response()->json(["status" => 1, "code" => 200, "message" => "Password changed successfully."]);
        }
        return response()->json(["status" => 0, "code" => 201, "message" => "User data not found", 'data' => $input]);
    }

    public function getAllCustomerStore(Request $request) {

        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required'
        ]);
        
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        $stores = \Corals\Modules\Marketplace\Models\Store::where(array('status' => 'active'))->get();
        if (isset($stores) && count($stores) > 0) {
            $validStores = [];
            foreach ($stores as $key=>$store) {

                if (isset($store->latitude) && isset($store->longitude)) {

                    $getDistance = \App\Helpers\Helper::distance($store->latitude, $store->longitude, $input['latitude'], $input['longitude'], "K");
		    	    
                    if ($getDistance <= $this->distance) {
                        $validStores['stores'][] = array( 
                            "id" =>  isset($store->id) ? $store->id:null,
                            "name"=> isset($store->name) ? $store->name:null,
                            "short_description"=> isset($store->short_description) ? $store->short_description:null,
                            "status"=> isset($store->status) ? $store->status:null,
                            "slug"=> isset($store->slug) ? $store->slug:null,
                            "address"=> isset($store->address) ? $store->address:null,
                            "email"=> isset($store->email) ? $store->email:null,
                            "phone_number"=> isset($store->phone_number) ? $store->phone_number:null,
                            "user_id"=> isset($store->user_id) ? $store->user_id:null,
                            "latitude"=> isset($store->id) ? $store->latitude:null,
                            "longitude"=> isset($store->id) ? $store->longitude:null,'image' => $store->getCoverPhotoAttribute());
                    }
                }
            }
            
            if(count($validStores) <= 0){
                
                $validStores['stores'] = [];
            }

            return response()->json(["status" => 1, "code" => 200, "message" => "", 'data' => $validStores]);
        }

        return response()->json(["status" => 1, "code" => 200, "message" => "No record found."]);
    }
    
    public function placeOrder(Request $request){
        
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'billing' => 'required',
            'user_id' => 'required',
            'store_id' => 'required',
            'order_details' => 'required' 
        ]);
        
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        
        /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
          
        $data = array(
            'order_number' => \Marketplace::createOrderNumber(),
            'amount' =>  $input['amount'],
            'currency' =>  'INR',
            'status' => 'pending',
            'shipping' => isset($input['shipping']) ? $input['shipping'] : null,
            'billing' => $input['billing'],
            'user_id' => $input['user_id'],
            'store_id' => $input['store_id'],
	     'created_by' => $input['user_id'],
            'updated_by' => $input['user_id']
        );
        $order_place = \Corals\Modules\Marketplace\Models\Order::create($data);
        
        if(isset($input['order_details']) && count($input['order_details']) > 0){
                
            foreach($input['order_details'] as $order){
                
                $order_data = [];
                $order_data = array(
                    
                    'amount' => $order['price'],
                    'quantity' =>  $order['quantity'],
                    'sku_code' => $order['skuid'],
                    'type' => 'Product',
                    'item_options' => null,
                    'order_id' => $order_place->id,
		    'created_by' => $input['user_id'],
            	    'updated_by' => $input['user_id'],
		    'description' => $order['description']
                );
                \Corals\Modules\Marketplace\Models\OrderItem::create($order_data);
            }

	    $userModel = \App\User::where(array('id' => $input['user_id']))->select('email','name')->first(); 
            $store = \Corals\Modules\Marketplace\Models\Store::where(array('id' => $input['store_id']))->first();
            $email_data = array('fname' => $userModel->name,'email' => $userModel->email,'order_number' => $order_place->order_number,
                                'store_user_email' => $store->user->email,'admin_mail' => 'helpdeliverdas@gmail.com',
                                'store_name' => $store->name,'user_address' => $input['billing'],
                                'order_items' => $input['order_details'],'totalamt' => $input['amount']);            
            
            
            \Illuminate\Support\Facades\Mail::send('emails.orderplace', $email_data  , function ($message) use($email_data) {
                $message->subject('Your Order placed successfully.')
                    ->from('helpdeliverdas@gmail.com', 'Deliverdas.com')
                    ->to(strtolower($email_data['email']))->bcc(array($email_data['store_user_email'],$email_data['admin_mail']));
            });
         
		 if(isset($input['notification_id'])){
	    $cutomer_notikey = $input['hardware_info']['notification_id'];
            $title_msg= "Your Order placed successfully with order price.".$input['amount'];
            $this->sendNotification($cutomer_notikey,$title_msg);
          }
        }
        
        return response()->json(["status" => 1, "code" => 200, "message" => "Order created successfully.", 'data' => array('order_id' => $order_place->id)]);
    }	

	public function getAddress(Request $request){
        

        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);
        
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        
        /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        $user = \App\User::where(array('id' => $input['user_id']))->first();
        
        if(isset($user)){
            $address = \GuzzleHttp\json_decode($user->address);
            $final_arr['address'] = array('billing' => isset($address->billing) ? (array) $address->billing : null,'shipping' => (array) $address->shipping);
            return response()->json(["status" => 1, "code" => 200, "message" => "", 'data' => $final_arr ]);     
        }
        return response()->json(["status" => 0, "code" => 201, "message" => "User not found."]);
    }

       public function getCustomerOrder(Request $request){
        
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);
        
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }
        
        /* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();
        
        $orders = \Corals\Modules\Marketplace\Models\Order::where(array('user_id' => $input['user_id']))->orderBy('created_at', 'DESC')->get();
        $complete_order = [];
        if (isset($orders) && count($orders) > 0) {
            foreach ($orders as $k => $order) {
                $market_order = [];
                $market_order['order_number'] = $order->order_number;
                $market_order['order_id'] = $order->id;
                $market_order['amount'] = $order->amount;
                $market_order['currency'] = $order->currency;
                $market_order['shipping'] = $order->shipping;
                $market_order['billing'] = $order->billing;
                $market_order['store_id'] = $order->store_id;
		$market_order['store_name'] = $order->store->name;
                $market_order['created_time'] = $order->created_at;
                $market_order['updated_time'] = $order->updated_at;

                if ($order->status == 'completed') {

                    $complete_order['complete'][] = $market_order;
                } else if ($order->status == 'pending') {

                    $complete_order['pending'][] = $market_order;
                } else if ($order->status == 'processing') {

                    $complete_order['processing'][] = $market_order;
                } else if ($order->status == 'canceled') {

                    $complete_order['canceled'][] = $market_order;
                }
            }
        }
        return response()->json(["status" => 1, "code" => 200, "message" => "", 'data' => $complete_order]);
        
    }
	
	    public function searchProduct(Request $request){
        
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'product_name' => 'required'
        ]);
        
        /** Return Error Message * */
        if ($validator->fails()) {
            $error_msg = [];
            foreach ($validator->messages()->all() as $key => $value) {
                array_push($error_msg, $value);
            }
            if ($error_msg) {
                return array(
                    'status' => 0,
                    'code' => 201,
                    'message' => $error_msg[0],
                    'data' => $request->all()
                );
            }
        }

 	/* store user latest hardware info */
        $hardwareInfo = new \App\HardwareInfo();
        $hardwareInfo->data = $input['hardware_info'];
        $hardwareInfo->Mysave();

        $product_name = $input['product_name'];
        $product_stores_modal = \Corals\Modules\Marketplace\Models\Product::where(function($query2) use ($product_name){ 
            $query2->where('name','like','%'.$product_name."%");})->select(array('store_id'))->distinct()->orderBy('created_at', 'DESC')->get();
              
        $validStores = [];
        if(isset($product_stores_modal) && count($product_stores_modal) > 0){
            foreach($product_stores_modal as $key => $store){
                
                if (isset($store->store) && isset($store->store->latitude) && isset($store->store->longitude)) {
                    $getDistance = \App\Helpers\Helper::distance($store->store->latitude, $store->store->longitude, $input['latitude'], $input['longitude'], "K");
                    if ($store->store->status == "active" && $getDistance <= $this->distance) {
                        $validStores['stores'][$key] = $store->store;
                        $validStores['stores'][$key]['image'] = $store->store->getCoverPhotoAttribute();
                    }
                }
            }
        }else{
            
            $product_category_modal = \Corals\Modules\Marketplace\Models\Category::where(function($query2) use ($product_name){ 
            $query2->where('name','like','%'.$product_name."%");})->select(array('id'))->distinct()->orderBy('created_at', 'DESC')->get();
            
            if(isset($product_category_modal) && count($product_category_modal) > 0){
                
                foreach($product_category_modal as $k => $product_cat){
                    
                    if(isset($product_cat->products[0]) && isset($product_cat->products[0]->store->latitude) && isset($product_cat->products[0]->store->longitude)){
                      
                        $getDistance = \App\Helpers\Helper::distance($product_cat->products[0]->store->latitude, $product_cat->products[0]->store->longitude, $input['latitude'], $input['longitude'], "K");
                        if($getDistance <= $this->distance && $product_cat->products[0]->store->status == "active"){
				$validStores['stores'][] = array( 
                                    "id" =>  isset($product_cat->products[0]->store->id) ? $product_cat->products[0]->store->id:null,
                                    "name"=> isset($product_cat->products[0]->store->name) ? $product_cat->products[0]->store->name:null,
                                    "short_description"=> isset($product_cat->products[0]->store->short_description) ? $product_cat->products[0]->store->short_description:null,
                                    "status"=> isset($product_cat->products[0]->store->status) ? $product_cat->products[0]->store->status:null,
                                    "slug"=> isset($product_cat->products[0]->store->slug) ? $product_cat->products[0]->store->slug:null,
                                    "address"=> isset($product_cat->products[0]->store->address) ? $product_cat->products[0]->store->address:null,
                                    "email"=> isset($product_cat->products[0]->store->email) ? $product_cat->products[0]->store->email:null,
                                    "phone_number"=> isset($product_cat->products[0]->store->phone_number) ? $product_cat->products[0]->store->phone_number:null,
                                    "user_id"=> isset($product_cat->products[0]->store->user_id) ? $product_cat->products[0]->store->user_id:null,
                                    "latitude"=> isset($product_cat->products[0]->store->latitude) ? $product_cat->products[0]->store->latitude:null,
                                    "longitude"=> isset($product_cat->products[0]->store->latitude) ? $product_cat->products[0]->store->longitude:null,
                                    'image' => $product_cat->products[0]->store->getCoverPhotoAttribute()
                                    );                           

				}


                        }
                        
                    }
                }
            }
	   	
	    if(count($validStores) == 0){
                $stores = \Corals\Modules\Marketplace\Models\Store::where(array('status' => 'active'))->orderBy('created_at', 'DESC')->get();
		
                if (isset($stores) && count($stores) > 0) {
                    foreach ($stores as $key => $store) {

                        if (isset($store->latitude) && isset($store->longitude)) {

                            $getDistance = \App\Helpers\Helper::distance($store->latitude, $store->longitude, $input['latitude'], $input['longitude'], "K");
                            if ($getDistance <= $this->distance) {
                               if ($getDistance <= $this->distance) {
                        	$validStores['stores'][] = array( 
                                            "id" =>  isset($store->id) ? $store->id:null,
                                            "name"=> isset($store->name) ? $store->name:null,
                                            "short_description"=> isset($store->short_description) ? $store->short_description:null,
                                            "status"=> isset($store->status) ? $store->status:null,
                                            "slug"=> isset($store->slug) ? $store->slug:null,
                                            "address"=> isset($store->address) ? $store->address:null,
                                            "email"=> isset($store->email) ? $store->email:null,
                                            "phone_number"=> isset($store->phone_number) ? $store->phone_number:null,
                                            "user_id"=> isset($store->user_id) ? $store->user_id:null,
                                            "latitude"=> isset($store->latitude) ? $store->latitude:null,
                                            "longitude"=> isset($store->longitude) ? $store->longitude:null,'image' => $store->getCoverPhotoAttribute()
                                    );
                                              
				}
                        }
                    }
                }
            }
        }
        return response()->json(["status" => 1, "code" => 200, "message" => "", 'data' => $validStores]);
    }
	

       
        
         public function sendNotification($cust_token,$title_msg){
        
        $fcmUrl = $this->fcm_url;
        $token = $cust_token;


        $notification = [
            'title' => $title_msg,
            "message" => $title_msg,
            'sound' => true
        ];

        $extraNotificationData = ["message" => $title_msg, "title" => $title_msg];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to' => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key='.$this->serverLKey,
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return true;
    }    public function sendData(){
        
        $this->sendNotification("eP5FaVylAMM:APA91bEhspWnaryR31rNIAQaBoBQGgbX572qiQk9eKcCYKHf7harAhHsU1n7MA0gBB4o_e9poJ7fyzpt6jMsnhXCRTRRB95KBvpAoDtyKiglLE9vN78QKY70kUtpj1IuNP-y9mF4C4dX", "hi hello what are you doing?");
    }
}
