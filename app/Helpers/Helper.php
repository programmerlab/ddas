<?php

declare(strict_types=1);

namespace App\Helpers;

use App\CorporateProfile;
use App\Interview;
use App\InterviewRating;
use App\RatingFeedback;
use App\User;
use Hash;
use Input;
use Mail;
use View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailerAutoload;

class Helper
{
    

    public static function setting(){

        $setting = \DB::table('settings')->select('field_key','field_value')->get()->toArray();
        $data = [];
        foreach ($setting as $key => $value) {

           $data[$value->field_key] = $value->field_value;
        }

        return (object) $data;


    }
    /**
     * function used to check stock in kit
     *
     * @param = null
     */
    public static function generateRandomString($length)
    {
        $key  = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }


    public static function FormatPhoneNumber($number)
    {
        return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number) . "\n";
    }
    
    public static function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }



    /* @method : send Mail
        * @param : email
        * Response :
        * Return : true or false
        */

    public function sendMailFrontEnd($email_content, $template)
    {
        $email_content['verification_token'] =  Hash::make($email_content['receipent_email']);
        $email_content['email']              = isset($email_content['receipent_email'])?$email_content['receipent_email']:'';
        //dd($email_content);
        $mail = new PHPMailer;
        $html = view::make('emails.' . $template, ['content' => $email_content]);
        $html = $html->render();

        try {
            $mail->isSMTP(); // tell to use smtp
            $mail->CharSet = 'utf-8'; // set charset to utf8

            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->Host       = isset(Helper::setting()->MAIL_HOST)?Helper::setting()->MAIL_HOST:getenv('MAIL_HOST'); // sets the SMTP server
            $mail->Port       = isset(Helper::setting()->MAIL_PORT)?Helper::setting()->MAIL_PORT:getenv('MAIL_PORT');
            $mail->SMTPSecure = isset(Helper::setting()->MAIL_ENCRYPTION)?Helper::setting()->MAIL_ENCRYPTION:getenv('MAIL_ENCRYPTION');                 // set the SMTP port for the GMAIL server
            $mail->Username   = isset(Helper::setting()->MAIL_USERNAME)?Helper::setting()->MAIL_USERNAME:getenv('MAIL_USERNAME'); // SMTP account username
            $mail->Password   = isset(Helper::setting()->MAIL_PASSWORD)?Helper::setting()->MAIL_PASSWORD:getenv('MAIL_PASSWORD');

            $username       =  isset(Helper::setting()->MAIL_USERNAME)?Helper::setting()->MAIL_USERNAME:getenv('MAIL_USERNAME');

            $email_from       =  isset(Helper::setting()->MAIL_FROM)?Helper::setting()->MAIL_FROM:getenv('MAIL_FROM');

            $mail->setFrom($username, $email_from);
            $mail->Subject = $email_content['subject'];
            $mail->MsgHTML($html);
            $mail->addAddress($email_content['receipent_email'], 'Admin');

            //$mail->addAttachment(‘/home/kundan/Desktop/abc.doc’, ‘abc.doc’); // Optional name
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->send();
            //echo "success";
        } catch (phpmailerException $e) {
        } catch (Exception $e) {
        }
    }
    /* @method : send Mail
      * @param : email
      * Response :
      * Return : true or false
      */
    public function sendMail($email_content, $template)
    {
        $billing     = $email_content['billing']     ?? null;
        $cart_detail = $email_content['cart_detail'] ?? null;

        $mail       = new PHPMailer;
        $html       = view::make('emails.' . $template, ['content' => $email_content,'billing' => $billing,'cart_detail' => $cart_detail]);
        $html       = $html->render();
        $subject    = $email_content['subject'];

        try {
            $mail->isSMTP(); // tell to use smtp
            $mail->CharSet = 'utf-8'; // set charset to utf8

            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->Host       = isset(Helper::setting()->MAIL_HOST)?Helper::setting()->MAIL_HOST:getenv('MAIL_HOST'); // sets the SMTP server
            $mail->Port       = isset(Helper::setting()->MAIL_PORT)?Helper::setting()->MAIL_PORT:getenv('MAIL_PORT');
            $mail->SMTPSecure = isset(Helper::setting()->MAIL_ENCRYPTION)?Helper::setting()->MAIL_ENCRYPTION:getenv('MAIL_ENCRYPTION');                 // set the SMTP port for the GMAIL server
            $mail->Username   = isset(Helper::setting()->MAIL_USERNAME)?Helper::setting()->MAIL_USERNAME:getenv('MAIL_USERNAME'); // SMTP account username
            $mail->Password   = isset(Helper::setting()->MAIL_PASSWORD)?Helper::setting()->MAIL_PASSWORD:getenv('MAIL_PASSWORD');

            $username       =  isset(Helper::setting()->MAIL_USERNAME)?Helper::setting()->MAIL_USERNAME:getenv('MAIL_USERNAME');
            $email_from     =  isset(Helper::setting()->MAIL_FROM)?Helper::setting()->MAIL_FROM:getenv('MAIL_FROM');

            $mail->setFrom($username, $email_from);
            $mail->Subject = $email_content['subject'];
            $mail->MsgHTML($html);
            $mail->addAddress($email_content['receipent_email'], $email_content['first_name']);


            //$mail->addAttachment(‘/home/kundan/Desktop/abc.doc’, ‘abc.doc’); // Optional name
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->send();
            //echo "success";
        } catch (phpmailerException $e) {
        } catch (Exception $e) {
        }
    }


    public function sendMailToAdmin($email_content, $template)
    {
        $mail       = new PHPMailer;
        $html       = view::make('emails.' . $template, ['content' => $email_content]);
        $html       = $html->render();
        $subject    = $email_content['subject'];

        try {
            $mail->isSMTP(); // tell to use smtp
            $mail->CharSet = 'utf-8'; // set charset to utf8


           $mail->SMTPAuth    = true;                  // enable SMTP authentication
            $mail->Host       = getenv('MAIL_HOST'); // sets the SMTP server
            $mail->Port       = 465; //getenv('MAIL_PORT');
            $mail->SMTPSecure = 'ssl';                 // set the SMTP port for the GMAIL server

            $mail->Username   = isset(Helper::setting()->MAIL_USERNAME)?Helper::setting()->MAIL_USERNAME:getenv('MAIL_USERNAME'); // SMTP account username
            $mail->Password   = isset(Helper::setting()->MAIL_PASSWORD)?Helper::setting()->MAIL_PASSWORD:getenv('MAIL_PASSWORD');

            $uname = isset(Helper::setting()->MAIL_USERNAME)?Helper::setting()->MAIL_USERNAME:getenv('MAIL_USERNAME'); // SMTP account username

            $sendMail = isset(Helper::setting()->MAIL_TO)?Helper::setting()->MAIL_TO:getenv('MAIL_TO');

            if(!$sendMail){
                $sendMail = $email_content['receipent_email'];
            }

            $mail->setFrom($uname, $email_content['from']);
            $mail->Subject = $email_content['subject'];
            $mail->MsgHTML($html);
            $mail->addAddress($sendMail, $email_content['first_name']);

            //$mail->addAttachment(‘/home/kundan/Desktop/abc.doc’, ‘abc.doc’); // Optional name
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->send();
            //echo "success";
        } catch (phpmailerException $e) {
        } catch (Exception $e) {
        }
    }


}
