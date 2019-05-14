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
use Modules\Admin\Models\Product;
use App\Models\Notification;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Dispatcher;
use Cookie;

class VendorController extends Controller
{
   /* @method : validateUser
    * @param : email,password,firstName,lastName
    * Response : json
    * Return : token and user details
    * Author : kundan Roy
    * Calling Method : get
    */

    public    $sid      = "";
    public    $token    = "";
    public    $from     = "";


    public function __construct(Request $request) {

        if ($request->header('Content-Type') != "application/json")  {
            $request->headers->set('Content-Type', 'application/json');
        }
        $user_id =  $request->input('userId');
    }
    // deactive user
    public function deactivateUser($user_id=null)
    {
         $user = User::find($user_id);
        /** Return Error Message **/
        if (!$user) {
                    $error_msg  =   [];
            foreach ( $validator->messages()->all() as $key => $value) {
                        array_push($error_msg, $value);
                    }

            return Response::json(array(
                'status' => 0,
                'code'=>500,
                'message' => 'Invalid User id',
                'data'  =>  $request->all()
                )
            );
        }
         $user->status=0;
         $user->save();

         return Response::json(array(
                'status' => 1,
                'code'=> 200,
                'message' => 'Account deativated',
                'data'  =>  []
                )
            );
    }

   

    public function createImage($base64)
    {
        try{
            $img  = explode(',',$base64);
            if(is_array($img) && isset($img[1])){
                $image = base64_decode($img[1]);
                $image_name= time().'.jpg';
                $path = storage_path() . "/image/" . $image_name;

                file_put_contents($path, $image);
                return url::to(asset('storage/image/'.$image_name));
            }else{
                if(starts_with($base64,'http')){
                    return $base64;
                }
                return false;
            }


        }catch(Exception $e){
            return false;
        }

    }
    


    public function updateKyc(Request $request, $userId){

        $document_name = ['adharCard','panCard','voterId','drivingLicense'];

        $vendor = Vendor::findOrNew(['user_id',$userId]);


        if(in_array($request->get('documentName'), $document_name)){
            $kyc    = Kyc::findOrNew(['vendor_id',$vendor->id,'document_name'=>$request->get('documentName')]);
        }else{
            $kyc    = Kyc::findOrNew(['vendor_id',$vendor->id]);
        }

        $kyc->document_name =  $request->get('documentName');
        $kyc->document_type =  $request->get('documentType');
        $kyc->vendor_id     =  $vendor->id;
        $kyc->is_verified   =  "No";
        $kyc->verified_by   =  "";
        $kyc->status        =  "Pending";

        return Response::json(array(
                    'status' => ($kyc)?1:0,
                    'code' => ($kyc)?200:404,
                    'message' => "Kyc updated and pending for review.",
                    'data'  => []
                    )
                );

    }

    // vendor update
    public function vendorUpdate(Request $request,$userId){

        $table_cname = \Schema::getColumnListing('vendors');
        $except = ['id','created_at','updated_at','shopType'];

        $vendor = Vendor::firstOrNew(['user_id'=>$userId]);
        $userId = User::find($userId);

        if($request->get('first_name') || $request->get('last_name')){
            $vendor->vendor_name = $request->get('first_name').' '.$request->get('last_name');
        }
        $vendor->type = $request->get('shopType');
        $vendor->role_type = $userId->role_type;


        if($request->get('profileImage')){
            $profile_image = $this->createImage($request->get('profileImage'));
            if($profile_image==false){
            }else{
                $vendor->profile_picture  = $profile_image;
            }

        }

        if($request->get('latitude')){
            $vendor->lat = $request->get('latitude');
        }
        if($request->get('longitude')){
            $vendor->lng = $request->get('longitude');
        }


        foreach ($table_cname as $key => $value) {

            if(in_array($value, $except )){
                continue;
            }
            if($request->get(camel_case($value))){
                $vendor->$value = $request->get(camel_case($value));
           }
        }

        try{
            $vendor->save();
            $status = 1;
            $code  = 200;
            $message ="Vendor Profile updated successfully";
        }catch(\Exception $e){
            $status = 0;
            $code  = 201;
            $message =$e->getMessage();
        }

        return response()->json(
                            [
                            "status" =>$status,
                            'code'   => $code,
                            "message"=> $message,
                            'data'=>[]
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
    public function updateProfile(Request $request,$userId)

    {
        $user = User::find($userId);
        $role       = Config::get('role');

        if((User::find($userId))==null)
        {
            return Response::json(array(
                'status' => 0,
                'code' => 201,
                'message' => 'Invalid user Id!',
                'data'  =>  []
                )
            );
        }

        $table_cname = \Schema::getColumnListing('users');
        $except = ['id','created_at','updated_at','profile_image','modeOfreach','email'];

        foreach ($table_cname as $key => $value) {

           if(in_array($value, $except )){
                continue;
           }
            if($request->get($value)){
                $user->$value = $request->get($value);
            }
        }

        if($request->get('profilePicture')){
            $profile_image = $this->createImage($request->get('profilePicture'));
            if($profile_image==false){
                return Response::json(array(
                    'status' => 0,
                     'code' => 201,
                    'message' => 'Invalid Image format!',
                    'data'  =>  $request->all()
                    )
                );
            }
            $user->profile_image  = $profile_image;
        }
        try{
            $user->save();
            $status = 1;
            $code  = 200;
            $message ="Profile updated successfully";
        }catch(\Exception $e){
            $status = 0;
            $code  = 201;
            $message =$e->getMessage();
        }

        return response()->json(
                            [
                            "status" =>$status,
                            'code'   => $code,
                            "message"=> $message,
                            'data'=>[]
                            ]
                        );

    }
  
    
  

    public function allCategory(Request $request)
    {

        $image_url  = env('IMAGE_URL',url::asset('storage/uploads/category/'));
        $catId      = null;
        $data        = [];
        try{ 

        $url = url('/'); 
        $data = \DB::table('categories')->select(\DB::raw('id as categoryId,category_name as categoryName'),\DB::raw('CONCAT("", "'.$url.'/", category_image) AS imagePath'))->where('parent_id',0)->get();


        }catch(\Exception $e){
            $data = [];
            $status = 0;
            $code   = 500;
            $msg    = $e->getMessage();
        }

        if(count($data)){
            $status = 1;
            $code   = 200;
            $msg    = "Category list found";
        }else{
            $status = 0;
            $code   = 404;
            $msg    = "Record not  found!";
        }

        return  response()->json([
                "totalItem" => isset($data)?$data->count():0,
                "status"=>$status,
                "code"=> $code,
                "message"=> $msg,
                'data' => $data
            ]
        );

    }
    // sub category
    public function subCategory(Request $request, $categoryId=null)
    { 

        $image_url  = env('IMAGE_URL',url::asset('storage/uploads/category/'));
        $catId      = null;
        $data        = [];
        try{ 

        $url = url('/'); 
        $data = \DB::table('categories')->select(\DB::raw('id as categoryId,parent_id as parentCategoryId,category_name as categoryName'),\DB::raw('CONCAT("", "'.$url.'/", category_image) AS imagePath')
        )->where(function($q)use($categoryId){
            $q->where('parent_id',$categoryId);
        })->get();
   
 
        }catch(\Exception $e){ dd($e);
            $data = [];
            $status = 0;
            $code   = 500;
            $msg    = $e->getMessage();
        }

        if(count($data)){
            $status = 1;
            $code   = 200;
            $msg    = "Category list found";
        }else{
            $status = 0;
            $code   = 404;
            $msg    = "Record not  found!";
        }

        return  response()->json([
                "totalItem" => isset($data)?$data->count():0,
                "status"=>$status,
                "code"=> $code,
                "message"=> $msg,
                'data' => $data
            ]
        );

    }

    public function showDefaultProducts(Request $request){


        try{ 
            

            $data = Product::with(['category'=> function($query){
                $url = url('/');
                $query->select('id','category_name','commission',\DB::raw('CONCAT("", "'.$url.'/", category_image) AS imagePath'));
            }])->with(['units'=> function($query){
                $query->select('id','name','full_name','description');
            }])->where(function ($query)  {
                $query->where('vendor_id',NULL);
            })
           // ->whereHas('vendorProduct')
            ->orderBy('id', 'desc')
            ->get(); 
 
        }catch(\Exception $e){ dd($e);
            $data = [];
            $status = 0;
            $code   = 500;
            $msg    = $e->getMessage();
        }

        if(count($data)){
            $status = 1;
            $code   = 200;
            $msg    = "Category list found";
        }else{
            $status = 0;
            $code   = 404;
            $msg    = "Record not  found!";
        }

        return  response()->json([
                "totalItem" => isset($data)?$data->count():0,
                "status"=>$status,
                "code"=> $code,
                "message"=> $msg,
                'data' => $data
            ]
        );

    }

     // sub category
    public function getProduct(Request $request, $vendorId=null)
    { 

        $image_url  = env('IMAGE_URL',url::asset('storage/uploads/category/'));
        $catId      = null;
        $data        = [];
        try{ 
            $productFromVendor = \DB::table('vendor_products')->where('vendor_id',$vendorId)->pluck('product_id')->toArray();

            $data = Product::with(['category'=> function($query){
                $url = url('/');
                $query->select('id','category_name','commission',\DB::raw('CONCAT("", "'.$url.'/", category_image) AS imagePath'));
            }])->with(['units'=> function($query){
                $query->select('id','name','full_name','description');
            }])->where(function ($query) use ($productFromVendor,$vendorId) {
                if (!empty($productFromVendor)) {
                    $query->orWhereIn('id', $productFromVendor);
                }
                 
                if($vendorId){  
                    $query->orWhere('vendor_id', $vendorId);
                }
            })
           // ->whereHas('vendorProduct')
            ->orderBy('id', 'desc')
            ->get(); 
 
        }catch(\Exception $e){ dd($e);
            $data = [];
            $status = 0;
            $code   = 500;
            $msg    = $e->getMessage();
        }

        if(count($data)){
            $status = 1;
            $code   = 200;
            $msg    = "Category list found";
        }else{
            $status = 0;
            $code   = 404;
            $msg    = "Record not  found!";
        }

        return  response()->json([
                "totalItem" => isset($data)?$data->count():0,
                "status"=>$status,
                "code"=> $code,
                "message"=> $msg,
                'data' => $data
            ]
        );

    }

    public function sendMail()
    {
        $emails = ['kroy@mailinator.com'];

        Mail::send('emails.welcome', [], function($message) use ($emails)
        {
            $message->to($emails)->subject('This is test e-mail');
        });
        var_dump( Mail:: failures());
        exit;
    }
    //array_msort($array, $cols)

    public function addPersonalMessage(Request $request){

        $rs = $request->all();
        $validator = Validator::make($request->all(), [
            'taskId' => "required",
            'userId' => "required",
            'comments'=> "required"
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
        $input=[];
        foreach ($rs as $key => $val){
            $input[$key] = $val;
        }

        \DB::table('messges')->insert($input);
            return response()->json(
                        [
                            "status" =>1,
                            'code' => 200,
                            "message" => "Message added successfully.",
                            'data' => $input
                        ]
        );
    }
    public function getPersonalMessage(Request $request){

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
        $posteduserid   = $request->get('postedUserId');
        $doerUserid     = $request->get('doerUserid');

        $data = Messges::with('commentPostedUser')
                    ->with(['taskDetails'=> function($q)use($posteduserid,$doerUserid,$request){
                        if($doerUserid){
                            $q->where('taskDoerId',$doerUserid);
                        }if($posteduserid){
                            $q->where('taskOwnerId',$posteduserid);
                        }
                    }])
                    ->where('taskId',$request->get('taskId'))
                    ->where(function($q)use($posteduserid,$doerUserid,$request){
                        if($posteduserid){
                            $q->where('userId',$posteduserid);
                        }
                        if($doerUserid){
                            $q->where('userId',$doerUserid);
                        }
                    })->get();

        return response()->json(
                        [
                            "status" =>count($data)?1:0,
                            'code' => count($data)?200:404,
                            "message" =>count($data)?"Message found":"Message not found",
                            'data' => $data
                        ]
        );
    }

    public function generateOtp(Request $request){
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

        $this->sendSMS($request->get('mobileNumber'),$otp);

        return response()->json(
                        [
                            "status"    =>  count($data)?1:0,
                            'code'      =>  count($data)?200:401,
                            "message"   =>  count($data)?"Otp generated":"Something went wrong",
                            'data'      =>  $data
                        ]
        );

    }

    public function verifyOtp(Request $request){
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
                    ->where('otp',$request->get('otp'))
                        ->where('userId',$request->get('userId'))->first();

        if($data){
             \DB::table('mobile_otp')
                    ->where('otp',$request->get('otp'))
                        ->where('userId',$request->get('userId'))->update(['is_verified'=>1]);

            \DB::table('users')
                        ->where('id',$request->get('userId'))
                        ->update(['phone'=>$data->mobile]);
        }

            return response()->json(
                            [
                                "status"    =>  count($data)?1:0,
                                'code'      =>  count($data)?200:500,
                                "message"   =>  count($data)?"Otp Verified":"Invalid Otp",
                                'data'      =>  $request->all()
                            ]
                );
    }

    public function sendSMS($mobileNumber=null,$otp=null)
    {
        $curl = curl_init();

            $modelNumber = $mobileNumber;
            $message = "Your verification OTP is : ".$otp;
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
    
    
    public function getTNC(){
        
        return \Illuminate\Support\Facades\View::make('TNC');
    }
}
