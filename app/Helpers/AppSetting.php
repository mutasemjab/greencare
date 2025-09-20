<?php

namespace App\Helpers;


class AppSetting
{

    static $FCM_SERVER_KEY = "AAAAEYxAi_E:APA91bFojYDuRwc38bVE6fAfQ_1D8uIBFtZ_XzzPPr_TfInnK1BiS8Ksb3fiZz2pGNBuaj9s1f7qAogs5LP4FL8W6Dsd8R1pcNQqCQmKE1gRHrTjKv0jBNCsi0RcFm0HZvw77vuKCCNv";


    public static function push_notification($token,$title,$body,$type,$order_id)
    {
      static $FCM_SERVER_KEY = "AAAAEYxAi_E:APA91bFojYDuRwc38bVE6fAfQ_1D8uIBFtZ_XzzPPr_TfInnK1BiS8Ksb3fiZz2pGNBuaj9s1f7qAogs5LP4FL8W6Dsd8R1pcNQqCQmKE1gRHrTjKv0jBNCsi0RcFm0HZvw77vuKCCNv";

        $customdata = array(
            "type" => $type,
            'sub_type'=> "",
            'category_id'=> "",
            'category_name'=> "",
            'item_id'=> "",
            "order_id" => $order_id,
        );

        if($title == ""){
            $title = "SheCart";
        }
        $msg = array(
            'body' =>$body,
            'title'=>$title,
            'sound'=>1/*Default sound*/
        );
        $fields = array(
            'to'           =>$token,
            'notification' =>$msg,
            'data'=> $customdata
        );
        $headers = array(
            'Authorization: key=' . $FCM_SERVER_KEY,
            'Content-Type: application/json'
        );
        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;
    }



}

