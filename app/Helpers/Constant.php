<?php
/**
 * Written By
 * Name: Putra;
 * Email: putra@jojonomic.com;
 * Created At: 2020-05-06 12:21:05
**/

namespace App\Helpers;

class Constant
{

    public static function response($error=true, $message='', $data=[]){
        if($error){
            $result = [
                'error' => true,
                'message' => $message,
            ];
        }else{
            $result = [
                'error' => true,
                'message' => $message,
                'data' => $data,
            ];
        }

        return json_encode($result);

    }

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    const EXCEPTION = 500;
    const UNAUTHORIZED = 401;
    const ERROR = 400;
    const SUCCESS = 200;


}
