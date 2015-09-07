<?php namespace App\Application\User;

use App\Model\UserBase;
use App\Model\UserImage;
use App\Model\Report;
use App\Model\UserLoginLog;

class UserService
{
    const TOKEN_COOKIE_NAME = 'admin_token';

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
            ->IsOpen()
            ->first();
        if (!$info) {
            return $this->outputFormat(false, '手机号码或者密码错误');
        }

        $image = UserImage::where('user_id', $info->user_id)
            ->head()
            ->first();
        $user = $info->toArray();
        if ($image) {
            $user['user_head'] = $image->path();
        } else {
            $user['user_head'] = UserImage::defaultImage();
        }
        $user['token'] = TokenService::tokenEncode($info->user_id);
        UserLoginLog::insert_login_log($info->user_id);
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
            $user['token'] = TokenService::tokenEncode($user_base->user_id);
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
     * @param $user_id
     * @return array
     */
    public function report($params, $user_id)
    {
        if (trim($params->get('report')) == '') {
            return [
                'status' => false,
                'msg' => '反馈内容不能为空!',
                'info' => [],
            ];
        }
        $report = new Report();
        $report->user_id = $user_id;
        $report->report = trim($params->get('report'));
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
     * @param $path
     * @param $user_id
     * @return array
     */
    public function updateUserHead($path, $user_id)
    {
        if (empty($path)) {
            return [
                'status' => false,
                'msg' => '没有上传头像!',
                'info' => [],
            ];
        }
        $user_image = new UserImage();
        $result = $user_image->where('user_id', $user_id)->where('type', UserImage::TYPE_HEAD)->first();
        if (empty($result)) {
            $user_image->user_id = $user_id;
            $user_image->type = UserImage::TYPE_HEAD;
            $user_image->image_url = $path;
            $user_image->save();
        } else {
            $user_image->where('user_id', $user_id)->where('type', UserImage::TYPE_HEAD)->update(
                [
                    'image_url' => $path
                ]
            );
        }
        return [
            'status' => true,
            'msg' => 'success',
            'info' => [],
        ];
    }

    /**
     * 设置新密码
     * @param $params
     * @param $user_id
     * @return array
     */
    public function setNewPassword($params, $user_id)
    {
        $result = UserBase::where('user_id', $user_id)
            ->update(['password' => $this->encryptPassword($params->get('newPassword'))]);
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

    /**
     * 搜索用户或者店铺
     * @param $params
     * @param $user_id
     * @return array
     */
    public function search($params, $user_id)
    {
        $result = UserBase::where('user_name', 'like', $params->get('searchContent') . '%')
            ->where('type', ($params->get('type') ?: UserBase::TYPE_BUSINESS))->where('user_id', '!=', $user_id)
            ->get()->toArray();
        if (empty($result)) {
            return [
                'status' => true,
                'msg' => 'success',
                'info' => [],
            ];
        }
        $list = [];
        foreach ($result as $value) {
            $list[] = [
                'id' => $value['id'],
                'name' => $value['user_name'],
            ];
        }
        return [
            'status' => true,
            'msg' => 'success',
            'info' => $list,
        ];
    }

    /**
     * @param $user_name
     * @param $password
     * @return array
     */
    public function adminLogin($user_name, $password)
    {
        if (!$user_name) {
            return [
                'status' => false,
                'msg' => '登录账户不能为空',
            ];
        }
        if (!$password) {
            return [
                'status' => false,
                'msg' => '密码不能为空',
            ];
        }

        $real_password = $this->encryptPassword($password);
        if (($user = UserBase::where('password', $real_password)//账户名或者手机都可以登录
            ->where('user_name', $user_name)->isOpen()->admin()->first()) ||
            (
            $user = UserBase::where('password', $real_password)
                ->where('user_cellphone', $user_name)->isOpen()->admin()->first()
            )
        ) {
            //加密token
            $token = TokenService::create($user->user_name, $user->user_id, $user->type);
            $cookie = \Cookie::forever(self::TOKEN_COOKIE_NAME, $token);
            \Cookie::queue($cookie);
            return [
                'status' => true,
                'msg' => '登录成功',
            ];
        }
        return [
            'status' => false,
            'msg' => '账户或密码错误',
        ];
    }

    /**
     * 获取用户列表
     * @param int $page
     * @param int $per_page
     * @return array
     */
    public function getList($page = 1, $per_page = 10)
    {
        $users = UserBase::offset(($page - 1) * $per_page)
            ->limit($per_page)
            ->get();
        if ($users->isEmpty()) {
            return [];
        }

        $user_list = [];
        foreach ($users as $user) {
            if ($user->type == UserBase::TYPE_ADMIN) {
                $type_text = '管理员';
            } elseif ($user->type == UserBase::TYPE_BUSINESS) {
                $type_text = '商家';
            } else {
                $type_text = '用户';
            }

            $user_list[] = array_merge(
                $user->toArray(),
                [
                    'type_text' => $type_text,
                    'status_text' => $user->isOpen() ? '已激活' : '未激活',
                    'is_active' => $user->isOpen(),
                ]
            );
        }
        return $user_list;
    }

    public function updateStatus($user_id, $status)
    {
        return UserBase::where('user_id', $user_id)
            ->update(
                [
                    'status' => $status
                ]
            );
    }
}
