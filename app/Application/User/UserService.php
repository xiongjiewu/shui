<?php namespace App\Application\User;

use App\Model\UserBase;
use App\Model\UserImage;
use App\Model\Report;

class UserService
{
    public function __construct()
    {

    }

    /**
     * 登录接口
     * @param $cellphone
     * @param $password
     * @return array
     */
    public function login($cellphone, $password)
    {
        if (!$cellphone) {
            return $this->outputFormat(false, '手机号码不能为空');
        }

        if (!$password) {
            return $this->outputFormat(false, '密码不能为空');
        }
        $password = $this->encryptPassword($password);
        $info = UserBase::where('user_cellphone', $cellphone)
            ->where('password', $password)
            ->isOpen()
            ->first();
        if (!$info) {
            return $this->outputFormat(false, '手机号码或者密码错误');
        }

        $image = UserImage::where('user_id', $info->user_id)
            ->head()
            ->first();
        $user = $info->toArray();
        if ($image) {
            $user['user_head'] = $image->url();
        } else {
            $user['user_head'] = UserImage::defaultImage();
        }
        return $this->outputFormat(true, 'success', $this->formatUser($user));
    }

    /**
     * 注册接口
     * @param $user_cellphone
     * @param $password
     * @param $type
     * @param array $image 图片数组，['url' => '','type' => '']
     * @param null $user_name
     * @param int $status
     * @return array
     */
    public function register($user_cellphone, $password, $type, $image = [], $user_name = null, $status = 1)
    {
        if (!$user_cellphone) {
            return $this->outputFormat(false, '手机号码不能为空', []);
        }

        if (!$password) {
            return $this->outputFormat(false, '密码不能为空', []);
        }

        if (!$type) {
            return $this->outputFormat(false, '注册的用户类型不能为空', []);
        }

        $user = UserBase::where('user_cellphone', $user_cellphone)
            ->first();
        if ($user) {
            return $this->outputFormat(false, '用户已存在', []);
        }

        $user_base = new UserBase();
        $user_base->user_cellphone = $user_cellphone;
        $user_base->password = $this->encryptPassword($password);
        $user_base->user_name = $user_name ?: '';
        $user_base->type = $type;
        $user_base->status = $status;
        if ($user_base->save()) {
            $user = $user_base->toArray();
            $image_url = UserImage::defaultImage();
            if (!empty($image['url']) && !empty($image['type'])) {
                $user_image = new UserImage();
                $user_image->user_id = $user_base->user_id;
                $user_image->image_url = $image['url'];
                $user_image->type = $image['type'];
                $user_image->save();
                $image_url = $user_image->url();
            }
            $user['user_head'] = $image_url;
            return $this->outputFormat(true, 'success', $this->formatUser($user));
        }
        return $this->outputFormat(false, '注册失败，请重新尝试', []);
    }

    /**
     * 格式化输出
     * @param $status
     * @param $msg
     * @param array $info
     * @return array
     */
    private function outputFormat($status, $msg, $info = [])
    {
        return [
            'status' => $status,
            'msg' => $msg,
            'info' => $info,
        ];
    }

    /**
     * 用户反馈
     * @param $params
     * @return array
     */
    public function report($params)
    {
        if (trim($params['report']) == '') {
            return [
                'status' => false,
                'msg' => '反馈内容不能为空!',
                'info' => [],
            ];
        }
        $report = new Report();
        $report->user_id = $params['userID'];
        $report->report = trim($params['report']);
        $report->save();
        return [
            'status' => true,
            'msg' => 'success',
            'info' => [],
        ];
    }

    /**
     * 加密密码
     * @param $password
     * @return string
     */
    public function encryptPassword($password)
    {
        return base64_encode(md5($password));
    }

    /**
     * @param $user
     * @return mixed
     */
    private function formatUser($user)
    {
        unset($user['password']);
        unset($user['created_at']);
        unset($user['updated_at']);
        unset($user['type']);
        unset($user['status']);
        return $user;
    }

    /**
     * 上传头像
     * @param $user_id
     * @param $head
     * @return array
     */
    public function updateUserHead($user_id, $head)
    {

    }

    /**
     * 设置新密码
     * @param $params
     * @return array
     */
    public function setNewPassword($params)
    {
        $result = UserBase::where('user_id', $params['userID'])
            ->update(['password' => $this->encryptPassword($params['newPassword'])]);
        if ($result) {
            return [
                'status' => true,
                'msg' => 'success',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'msg' => '修改密码失败!',
                'info' => [],
            ];
        }
    }
}