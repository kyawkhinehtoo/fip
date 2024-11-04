<?php

namespace App\Traits;

use App\Models\Incident;
use App\Models\SmsGateway;
use Illuminate\Support\Facades\DB;
use Datetime;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Illuminate\Support\Facades\Storage;
use ArrayIterator;
use CachingIterator;

trait SMSTrait
{
    static $sms_post_url = 'https://api.smsbrix.com/v1/message/send';
    static $sms_status_url = 'https://api.smsbrix.com/v1/message/info/';
    static $sid;
    static $token;
    static $sender_id;
    static $header;
    static $site_url;

    private function sanitisePhone($phone_no)
    {
        $phone_no = preg_replace('/[^0-9]/', '', $phone_no); // Remove non-numeric characters

        $pattern_9 = "/^9[0-9]{7,9}$/";
        $pattern_09 = "/^09[0-9]{7,9}$/";
        $pattern_959 = "/^959[0-9]{7,9}$/";
        $pattern_plus959 = "/^\+959[0-9]{7,9}$/";
        $pattern_zero959 = "/^0959[0-9]{7,9}$/";
        $pattern_overnum = "/^099[0-9]{9,12}$/";
        $new_phone = $phone_no;

        switch (true) {


            case preg_match($pattern_959, $phone_no):
                // 959 pattern
                $new_phone = '09' . substr($phone_no, 3);
                break;

            case preg_match($pattern_9, $phone_no):
                // 9 pattern
                $new_phone = '0' . $phone_no;
                break;

            case preg_match($pattern_plus959, $phone_no):
                // +959 pattern
                $new_phone = '09' . substr($phone_no, 4);

                break;

            case preg_match($pattern_zero959, $phone_no):
                // 0959 pattern
                $new_phone = '09' . substr($phone_no, 4);
                break;

            case preg_match($pattern_09, $phone_no):
                // 09 pattern
                break;

            case preg_match($pattern_overnum, $phone_no):
                // overnum pattern
                $new_phone = '09' . substr($phone_no, 3);
                break;

            default:
                // No pattern matched, keep the original phone number
                break;
        }
        return $new_phone;
    }




    public function checkPhoneArray($cus_phone)
    {

        $phones = $cus_phone;
        $data = "";
        $delimiters = array(",", ";", ":", "/");
        $cus_phone = str_replace($delimiters, ",", $cus_phone);
        $phones = $cus_phone;
        if (strpos($cus_phone, ',') !== false) {
            $phones = explode(",", $cus_phone);
        }

        if (is_array($phones)) {
            $phones = array_unique($phones);
            $iter = new CachingIterator(new ArrayIterator($phones));

            foreach ($iter as $phone) {
                $data .= $this->sanitisePhone($phone);
                if ($iter->hasNext()) {
                    $data .= ",";
                }
            }
        } else {
            $data = $this->sanitisePhone($phones);
        }
        return $data;
    }
    public function deliverSMS($phones, $email_body)
    {
        $success = false;
        $phones = $this->checkPhoneArray($phones);
        Storage::append('phone.log', $phones);
        if (strpos($phones, ',') !== false) {
            $phones = explode(",", $phones);
        }

        if (is_array($phones)) {
            foreach ($phones as $phone) {
                // $pattern = "/^(09|\+959)+[0-9]+$/";
                // if (!preg_match($pattern, $phone)) {
                //     $phone = '09' . $phone;
                // }

                $sms_response = $this->sendSMS($phone, $email_body);

                if ($sms_response['status'] == 'success') {
                    $client = new \GuzzleHttp\Client();
                    $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                    $statusresponseBody = json_decode($status_response->getBody(), true);
                    if ($statusresponseBody['status'] == 'Sent') {
                        $success = true;
                    }
                    //  $success = 'sent';
                }
            }
        } else {

            $sms_response = $this->sendSMS($phones, $email_body);

            if ($sms_response['status'] == 'success') {
                $client = new \GuzzleHttp\Client();
                $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                $statusresponseBody = json_decode($status_response->getBody(), true);
                if ($statusresponseBody['status'] == 'Sent') {
                    $success = true;
                }
                // $success = 'sent';
            }
        }
        return $success;
    }
    public function sendSMS($phone, $message)
    {
        // $pattern = "/^09[0-9]{7,9}$/";
        // if (preg_match($pattern, $phone)) {
        //     Storage::append('sms.log', $phone . " : " . $message);
        //     return array('status' => 'success');
        // }
        // return array('status' => 'fail');
        $smsgateway = SmsGateway::first();

        if ($smsgateway) {
            if ($smsgateway->status == '1') {
                self::$sid = $smsgateway->sid;
                self::$token = $smsgateway->token;
                self::$sender_id = $smsgateway->sender_id;
                self::$header = ['Authorization' => 'Basic ' . base64_encode($smsgateway->sid . ':' . $smsgateway->token)];
            }

            self::$site_url = env('APP_URL', 'https://localhost:8000');
            $postInput  =  [
                'senderid' => self::$sender_id,
                'number' => trim($phone),
                'message' => trim($message),
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', self::$sms_post_url, ['form_params' => $postInput, 'headers' => self::$header]);
            $responseBody = json_decode($response->getBody(), true);
            //   $responseBody = array('status' => 'success');
            return $responseBody;
        }
        return false;
    }
}
