<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SendSMS
 *
 * @author kapi7001
 */

namespace App\helper;

class SendSMS {

    //put your code here
    public $mobile_no;
    public $sms_api_url = "";
    public $message;
    public $url;
    public $tinyURL;

    function __construct($mobile_no, $message,$url=null) {

        $this->message = $message;
        $this->mobile_no = $mobile_no;
        $this->url = $url;
    }
    
    public function createOnlyLink(){
        
        $username = env('TINY_URL_USERNAME');
        $password = \App\helper\gobalFunction::getDecPass(env('TINY_URL_PASSWORD'));
        $api_url =  env('TINY_URL');

        // Init the CURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);            
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_POST, 1);              
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(    
                'url' => $this->url,
                'format'   => 'json',
                'action'   => 'shorturl',
                'username' => $username,
                'password' => $password
            ));
        
        $data = curl_exec($ch);
        curl_close($ch);
        
        if(isset($data)){
            
            $jsonData = json_decode($data,true);
            if(isset($jsonData['shorturl']) && $jsonData['shorturl'] != null){
                return $jsonData['shorturl'];
            }
        }
        return 0;
    }
    
    public function createTinyLink(){
        
        $tinyLink = $this->createOnlyLink();
        if($tinyLink && $tinyLink != null){
            $this->tinyURL = $tinyLink;
            $this->message = str_replace("*",$tinyLink,$this->message);
            $res = $this->sendSMS();
            return $res;
        }   
    }
    
    public function sendSMS(){
        
       
        $post = array(
            'message' => $this->message,
            'number' => \Illuminate\Support\Facades\Config::get('constants.SMS_COUNTRY_CODE') . $this->mobile_no,
            'username' => \Illuminate\Support\Facades\Config::get('constants.SMS_USERNAME'),
            'senderid' => \Illuminate\Support\Facades\Config::get('constants.SENDER_ID'),
            'secret' => \Illuminate\Support\Facades\Config::get('constants.SENDER_SECRETKEY')
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_URL, $this->sms_api_url_uk);

        $resp_create_json = curl_exec($ch);
        $res = json_decode($resp_create_json, true);
        if (isset($res['message'])) {
            $res_sms = $res['message'];
            if (isset($res['responseCode'])) {
                $res_sms .= " " . $res['responseCode'];
            }
            return $res_sms;
        } else {

            return "failed";
        }
    } 
    

}
