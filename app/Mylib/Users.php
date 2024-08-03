<?php

namespace App\Mylib;

use App\Helper\Mycurl;
use Illuminate\Http\Request;


class Users
{
    private $user = [];

    public function __construct()
    {

    }

    public function checklogin($params = [])
    {
        try {
            $access_token = session('access_token');
            if($access_token){
                $getuser = $this->getUserInfo($access_token);
                if(isset($getuser['id']) && $getuser['id'] > 0) {
                    $this->user = $getuser;
                    return true;
                }
            }else{
                $params = [
                    'email' => 'tutd@horusvn.com',
                    'password' => 'Chamchi123'
                ];

                $login = $this->loginWorkHorus($params);

                if(isset($login['access_token']) && $login['access_token'] != ""){
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }

    }

    public function getuser()
    {
        return $this->user;
    }

    public function getUserInfo($access_token){
        try {
            $url_api = env('WORK_HORUS').'/api/home/get-info';
            $result = Mycurl::getCurl($url_api, $access_token);

            return $result;

        } catch (\Exception $e){
            return $e->getMessage();
        }

    }

    public function loginWorkHorus($params)
    {
        try {
            $url_api = env('WORK_HORUS').'/api/login';
            $result = Mycurl::postCurl($url_api, '', $params);
            if(isset($result['access_token']) && !empty($result['access_token'])){
                $access_token = $result['access_token'];
                session(['access_token' => $access_token]);
            }

            return $result;

        }catch (\Exception $e){
            return [
                'message' => 'Đăng nhập thất bại',
                'error' => $e->getMessage()
            ];
        }

    }
}
