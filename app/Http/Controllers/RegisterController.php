<?php namespace App\Http\Controllers;

use App\Application\User\UserService;
use App\Application\VerifyService;
use App\Model\UserBase;
use App\Model\UserImage;
use Illuminate\Http\Request;
use \Response;

class RegisterController extends Controller
{
    /**
     * 用户注册
     * @param Request $request
     * @return mixed
     */
    public function userRegister(Request $request)
    {
        $cellphone = $request->get('cellphone');
        $password = $request->get('password');
        $password2 = $request->get('password2');
        $user_name = $request->get('user_name');
        $verify = $request->get('verify');
        $head = $request->get('avatar_url');
        if (!$cellphone || !$password || !$password2 || !$verify) {
            return $this->fail('参数错误');
        }
        //检查登录信息
        $check = $this->check($cellphone, $password, $password2, $verify, $head, UserBase::TYPE_USER, $user_name);
        if ($check['status'] == 'error') {
            return $this->fail($check['message']);
        }
        return Response::json([
            'code' => 0,
            'message' => '注册成功！',
            'userInfo' => $check['userInfo'],
        ]);
    }

    /**
     * 商户注册
     * @param Request $request
     * @return mixed
     */
    public function BusinessRegister(Request $request)
    {
        $cellphone = $request->get('cellphone');
        $password = $request->get('password');
        $password2 = $request->get('password2');
        $verify = $request->get('verify');
        $user_name = $request->get('user_name');
        if (!$cellphone || !$password || !$password2 || !$verify) {
            return $this->fail('参数错误');
        }
        //检查登录信息
        $check = $this->check($cellphone, $password, $password2, $verify, '', UserBase::TYPE_BUSINESS, $user_name);
        if ($check['status'] == 'error') {
            return $this->fail($check['message']);
        }
        return Response::json([
            'code' => 0,
            'message' => '注册成功！',
            'userInfo' => $check['userInfo'],
        ]);
    }

    /**
     * @param $cellphone
     * @param $password
     * @param $password2
     * @param $verify
     * @param $head
     * @param $type
     * @param $user_name
     * @return array
     */
    private function check($cellphone, $password, $password2, $verify, $head, $type, $user_name)
    {
        if ($password != $password2) {
            return [
                'status' => 'error',
                'message' => '2次密码不一致',
                'userInfo' => [],
            ];
        }

        $rt = $this->checkVerify($cellphone, $verify);

        if (!$rt['status']) {
            return [
                'status' => 'error',
                'message' => $rt['message'],
                'userInfo' => [],
            ];
        }

        $path = '';
        if (!empty($head)) {
            $path = $head;
        }
        $image = [
            'url' => $path,
            'type' => UserImage::TYPE_HEAD,
        ];


        $register = (new UserService())->register($cellphone, $password, $type, $image, $user_name);

        if ($register['status']) {
            return [
                'status' => 'ok',
                'message' => 'success',
                'userInfo' => $register['info'],
            ];
        }
        return [
            'status' => 'error',
            'message' => $register['message'],
            'userInfo' => [],
        ];
    }

    /**
     * 检测手机号
     * @param $cellphone
     * @param $verify
     * @return bool
     */
    private function checkVerify($cellphone, $verify)
    {
        return (new VerifyService())->checkThisPhoneVerifyIsTrue($cellphone, $verify);
    }

    /**
     * 发送验证码
     * @param Request $request
     * @return mixed
     */
    public function verify(Request $request)
    {
        $cellphone = $request->get('cellphone');
        $check = (new VerifyService())->sendThisPhoneVerify($cellphone);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '发送成功！',
                    'userInfo' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 第三方平台注册
     * @param Request $request
     * @return mixed
     */
    public function otherRegister(Request $request)
    {
        $check = (new UserService())->otherRegister($request);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '注册登入成功！',
                    'userInfo' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }
}