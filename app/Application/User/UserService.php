<?php namespace App\Application;

use App\Model\UserBase;
use App\Model\UserImage;

class UserService
{
    public function __construct()
    {

    }

    public function login($cellphone, $password)
    {
        if (!$cellphone) {
            return [
                'status' => false,
                'msg' => '手机号码不能为空',
                'info' => [],
            ];
        }

        if (!$password) {
            return [
                'status' => false,
                'msg' => '密码不能为空',
                'info' => [],
            ];
        }
        $password = base64_encode(md5($password));
        $info = UserBase::where('user_cellphone', $cellphone)
            ->where('password', $password)
            ->first();
        if (!$info) {
            return [
                'status' => false,
                'msg' => '手机号码或者密码错误',
                'info' => [],
            ];
        }

        $image = UserImage::where('user_id', $info->user_id)
            ->head()
            ->first();
        $user = $info->toArray();
        if ($image) {
            $user['user_head'] = $image->url();
        } else {
            $user['user_head'] = '';
        }
        return [
            'status' => true,
            'msg' => 'success',
            'info' => $user,
        ];
    }
}