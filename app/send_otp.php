<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

class send_otp extends Model
{
    protected $table = 'send_otp';
    protected $primaryKey = 'id';
    protected $fillable = ['mobile_no','email_id','ip_address','user_agent','otp','otp_status','sms_status','fname','lname','http_referer'];
    
    
    
    public function PasswordCheck($attribute, $value, $parameters,Validator $validator){
        
        $email_id = array_get($validator->getData(), $parameters[0], null);
        if ($email_id) {

            $email_ex = explode("@", $email_id);
            if (isset($email_ex[0])) {

                if (strcasecmp($value, $email_ex[0]) == 0) {
                    return false;
                }
            }
        } else {
            return false;
        }
        return true;
    }
    
    public function MobileCheck($attribute, $value, $parameters,Validator $validator){
        
        if (isset($value)) {
            
            $sendOTPModel = send_otp::where(array('mobile_no' => $value,'otp_status'=> 1))->first();
            if(isset($sendOTPModel) && isset($sendOTPModel->id)){

                return false;
            }

            $LUUSER = \Corals\User\Models\User::where(array('phone_number' => $value))->first();
            if(isset($LUUSER) && isset($LUUSER->id)){

               return false; 
            }

        }else{
            
           return false;  
        }
        return true;
    }
    
    
    public function EmailCheck($attribute, $value, $parameters,Validator $validator){
        
        $email = $value;
        if (isset($email)) {
            
            $sendOTPModel = send_otp::where(array('email_id' => $email,'otp_status'=> 1))->first();
            if(isset($sendOTPModel) && isset($sendOTPModel->auto_id)){

                return false;
            }
	   $LUUSER = \Corals\User\Models\User::where(array('email' => $email))->first();
            if(isset($LUUSER) && isset($LUUSER->id)){

               return false; 
            }

        }else{
            
            return false; 
        }
        return true;
    }
}
