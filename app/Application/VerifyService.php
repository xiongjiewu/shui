<?php namespace App\Application;

use App\Model\UserVerify;

class VerifyService
{
    /**
     * 检查手机号码和验证码是否一致
     * @param $cellphone
     * @param $verify
     * @return Bool
     */
    public function checkThisPhoneVerifyIsTrue($cellphone, $verify)
    {
        $user_verify_model = new UserVerify();
        $rt = $user_verify_model->where('cellphone', $cellphone)->where('verify', $verify)->first();
        if (empty($rt)) {
            return [
                'status' => false,
                'message' => '验证码不正确!',
                'info' => [],
            ];
        }
        if ($rt->status == UserVerify::STATUS_FALSE) {
            return [
                'status' => false,
                'message' => '验证码已经被使用!',
                'info' => [],
            ];
        }
        if ($rt->expired_at < time()) {
            return [
                'status' => false,
                'message' => '验证码已经过期!',
                'info' => [],
            ];
        }
        //通过所有的验证
        $bool = $user_verify_model->where('id', $rt->id)->update(
            [
                'status' => UserVerify::STATUS_FALSE,
            ]
        );
        if($bool){
            return [
                'status' => true,
                'message' => '验证成功!',
                'info' => [],
            ];
        }else{
            return [
                'status' => false,
                'message' => '系统一个人去旅行了，请重试!',
                'info' => [],
            ];
        }
    }

    /**
     * 发送验证码短信给用户
     * @param $phone
     * @return mix
     */
    public function sendThisPhoneVerify($phone)
    {
        if (strlen(trim($phone)) != 11) {
            return [
                'status' => false,
                'message' => '手机号错误!',
                'info' => [],
            ];
        }
        $user_verify_model = new UserVerify();
        $verify = rand(pow(10, 5), pow(10, 6) - 1);
        $content = '您的验证码是：' . $verify . '。有效时间为' . ((int)$user_verify_model->getExpiredTime() / 60) . '分钟，请不要把验证码泄露给其他人。【水想世界】';
        $user_verify_model->cellphone = $phone;
        $user_verify_model->verify = $verify;
        $user_verify_model->expired_at = (time() + (int)$user_verify_model->getExpiredTime());
        $rt = $user_verify_model->save();
        if ($rt) {
            $bool = $user_verify_model->getSendMsgUrl($phone, $content);
            if ($bool) {
                return [
                    'status' => true,
                    'message' => '发送成功!',
                    'info' => [],
                ];
            } else {
                return [
                    'status' => true,
                    'message' => '发送失败!',
                    'info' => [],
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => '系统一个人旅行去了,请重试!',
                'info' => [],
            ];
        }
    }
}