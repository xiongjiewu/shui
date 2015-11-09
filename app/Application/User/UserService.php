<?php namespace App\Application\User;

use App\Model\UserBase;
use App\Model\UserBlackWater;
use App\Model\UserCompanyExtend;
use App\Model\UserFinancial;
use App\Model\UserImage;
use App\Model\Report;
use App\Model\UserLoginLog;
use App\Model\UserThirdParty;
use Carbon\Carbon;

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
            ->where('password', $password)->User()->IsOpen()->first();

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
     * 商户登录接口
     * @param $cellphone
     * @param $password
     * @return array
     */
    public function businessLogin($cellphone, $password)
    {
        if (!$cellphone) {
            return $this->outputFormat(false, '手机号码不能为空');
        }

        if (!$password) {
            return $this->outputFormat(false, '密码不能为空');
        }

        $password = $this->encryptPassword($password);
        $info = UserBase::where('user_cellphone', $cellphone)
            ->where('password', $password)->Business()->IsOpen()->first();

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
        $user_base->user_name = !empty($user_name) ? $user_name : '';
        $user_base->type = $type;
        $user_base->status = $status;
        $user_base->invite_code = $user_cellphone . mt_rand(100, 999);

        if ($user_base->save()) {

            $user = $user_base->toArray();
            $image_url = UserImage::defaultImage();
            if (!empty($image['url']) && !empty($image['type'])) {
                $user_image = new UserImage();
                $user_image->user_id = $user_base->user_id;
                $user_image->image_url = $image['url'];
                $user_image->type = $image['type'];
                $user_image->is_completion = UserImage::IS_COMPLETION_QINIU;
                $user_image->save();
                $image_url = $user_image->path();
            }

            if ($type == UserBase::TYPE_USER && UserFinancial::getInitialize() > 0) {
                $user_financial = new UserFinancial();
                $user_financial->user_id = $user_base->user_id;
                $user_financial->water_count = UserFinancial::getInitialize();
                $user_financial->save();
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
            'message' => $msg,
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
            'message' => 'success',
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
                'message' => '没有上传头像!',
                'info' => [],
            ];
        }
        $user_image = new UserImage();
        $result = $user_image->where('user_id', $user_id)->where('type', UserImage::TYPE_HEAD)->first();
        if (empty($result)) {
            $user_image->user_id = $user_id;
            $user_image->type = UserImage::TYPE_HEAD;
            $user_image->image_url = $path;
            $user_image->is_completion = UserImage::IS_COMPLETION_QINIU;
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
            'message' => 'success',
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
                'message' => 'success',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'message' => '修改密码失败!',
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
                'message' => 'success',
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
            'message' => 'success',
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
                'message' => '登录账户不能为空',
            ];
        }
        if (!$password) {
            return [
                'status' => false,
                'message' => '密码不能为空',
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
                'message' => '登录成功',
            ];
        }
        return [
            'status' => false,
            'message' => '账户或密码错误',
        ];
    }

    /**
     * 修改保存
     * @param $user_id
     * @param $request
     */
    public function update($user_id, $request)
    {

    }

    /**
     * 展示列表 type-1用户 2-商户
     * @param $type
     * @param $requset
     * @return array
     */
    public function show($type, $requset)
    {
        return $this->formatUsers([UserBase::where('user_id', $requset->get('user_id'))->first()]);
        $data = [];
        $user_result = UserBase::where('user_id', $requset->get('user_id'))->first();
        $user_black_rt = UserBlackWater::whereIn('user_id', $requset->get('user_id'))->first();
        $data['user_id'] = $requset->get('user_id');
        $data['user_cellphone'] = $user_result->user_cellphone;
        $data['user_name'] = $user_result->user_name;
        $data['invite_code'] = $user_result->invite_code;
        $user_financial = UserFinancial::where('user_id', $requset->get('user_id'))->first();
        $data['water_count'] = $user_financial ? $user_financial->water_count : 0;
        $data['black_water'] = $user_black_rt ? $user_black_rt->black_water : 0;
        $data['price'] = $user_financial ? $user_financial->price : 0;
        $data['send_water'] = $user_financial ? $user_financial->send_water : 0;
        $data['public_count'] = $user_financial ? $user_financial->public_count : 0;
        $data['giving'] = $user_financial ? $user_financial->giving : 0;
        switch ($type) {
            case 1:
                $user_image = UserImage::where('user_id', $requset->get('user_id'))->head()->first();
                $data['image_url'] = $user_image ? $user_image->path() : '';
                $user_third_party = UserThirdParty::where('user_id', $requset->get('user_id'))->get();
                $data['wei_xin'] = '';
                $data['qq'] = '';
                foreach ($user_third_party as $user_third_party_v) {
                    if ($user_third_party_v->type == 1) {
                        $data['wei_xin'] = $user_third_party_v->user_other_id;
                    } else {
                        $data['qq'] = $user_third_party_v->user_other_id;
                    }
                }
                return $data;
            case 2:
                $user_image = UserImage::where('user_id', $requset->get('user_id'))->get();
                $images = [];
                if (!empty($user_image)) {
                    foreach ($user_image as $user_image_val) {
                        $images[$user_image_val->id] = $user_image_val->path();
                    }
                }
                return $data;
        }
    }

    /**
     * 获取用户列表 TODO需要分页 把商户和用户列表页分开
     * @param int $page
     * @param int $per_page
     * @param int $type 1-用户 2-商户
     * @return array
     */
    public function getList($page = 1, $per_page = 10, $type = 1)
    {
        //type 1为用户 2为商户
        $users = [];
        switch ($type) {
            case 1:
                $users = UserBase::offset(($page - 1) * $per_page)
                    ->user()->limit($per_page)
                    ->get();
                break;
            case 2:
                $users = UserBase::offset(($page - 1) * $per_page)
                    ->business()->limit($per_page)
                    ->get();
                break;
        }

        return $this->formatUsers($users);
    }

    protected function formatUsers($users)
    {
        if (!$users) {
            return [];
        }
        $user_list = [];
        $user_ids = [];
        foreach ($users as $user) {

            $user_ids[] = $user['user_id'];

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
                    'image_url' => '',
                    'water_count' => 0, //亲水值
                    'black_water' => 0,//黑水值
                    'send_water' => 0,  //发送的水值
                    'public_count' => 0,    //公益值
                    'giving' => 0,   //商户设置的可以被领取的值
                    'user_address' => '',
                    'user_desc' => '',
                    'user_http' => '',
                    'user_company_lat' => '',
                    'user_company_lng' => '',
                ]
            );
        }

        //获得头像信息
        $user_image_rt = UserImage::whereIn('user_id', $user_ids)->Head()->get();
        foreach ($user_list as $user) {
            foreach ($user_image_rt as $user_image_rt_v) {
                if ($user['user_id'] == $user_image_rt_v->user_id) {
                    $user['image_url'] = $user_image_rt_v->path();
                }
            }
        }

        //获得亲水值
        $user_financial_rt = UserFinancial::whereIn('user_id', $user_ids)->get();
        foreach ($user_list as $user) {
            foreach ($user_financial_rt as $user_financial_rt_v) {
                if ($user['user_id'] == $user_financial_rt_v->user_id) {
                    $user['water_count'] = $user_financial_rt_v->water_count;
                    $user['send_water'] = $user_financial_rt_v->send_water;
                    $user['public_count'] = $user_financial_rt_v->public_count;
                    $user['giving'] = $user_financial_rt_v->giving;
                }
            }
        }

        //获得黑水值
        $user_black_rt = UserBlackWater::whereIn('user_id', $user_ids)->get();
        foreach ($user_list as $user) {
            foreach ($user_black_rt as $user_black_rt_v) {
                if ($user['user_id'] == $user_black_rt_v->user_id) {
                    $user['black_water'] = $user_black_rt_v->send_water;
                }
            }
        }

        //获得公司信息
        $user_company_extend_rt = UserCompanyExtend::whereIn('user_id', $user_ids)->get();
        foreach ($user_list as $user) {
            foreach ($user_company_extend_rt as $user_company_extend_rt_v) {
                if ($user['user_id'] == $user_company_extend_rt_v->user_id) {
                    $user['user_address'] = $user_company_extend_rt_v->user_address;
                    $user['user_company_name'] = $user_company_extend_rt_v->user_company_name;
                    $user['user_desc'] = $user_company_extend_rt_v->user_desc;
                    $user['user_http'] = $user_company_extend_rt_v->user_http;
                    $user['user_company_lat'] = $user_company_extend_rt_v->user_company_lat;
                    $user['user_company_lng'] = $user_company_extend_rt_v->user_company_lng;
                }
            }
        }

        return $user_list;

    }

    public
    function updateStatus($user_id, $status)
    {
        return UserBase::where('user_id', $user_id)
            ->update(
                [
                    'status' => $status
                ]
            );
    }

    /**
     * 第三方平台注册
     * @param $params
     * @return array
     */
    public
    function otherRegister($params)
    {
        if (!$params->get('open_id')) {
            return [
                'status' => false,
                'message' => '用户唯一标识不能为空!',
            ];
        }
        if (!$params->get('type')) {
            return [
                'status' => false,
                'message' => '登入来源标识不能为空!',
            ];
        }
        $user_third_parth = new UserThirdParty();
        $rt = $user_third_parth->where('user_other_id', $params->get('open_id'))
            ->where('type', $params->get('type'))->first();
        $user_base = new UserBase();
        $user_image = new UserImage();
        //如果没注册过
        if (empty($rt) && in_array($params->get('type'), [UserThirdParty::TX_QQ, UserThirdParty::WEI_XIN])) {
            $user_base->invite_code = time() . mt_rand(100, 999);
            if ($params->get('nick_name')) {
                $user_base->user_name = $params->get('nick_name');
            }
            $user_base->save();
            if ($user_base->user_id) {
                $user_third_parth->user_other_id = $params->get('open_id');
                $user_third_parth->type = $params->get('type');
                $user_third_parth->user_id = $user_base->user_id;
                $user_third_parth->save();
                $image = $user_image->where('user_id', $user_base->user_id)->head()->first();
                if (empty($image)) {
                    $user_image->image_url = $params->get('head_img') ?: UserImage::defaultImage();
                    $user_image->user_id = $user_base->user_id;
                    $user_image->is_completion = UserImage::IS_COMPLETION_TRUE;
                    $user_image->type = UserImage::TYPE_HEAD;
                    $user_image->save();
                } else {
                    $user_image->where('user_id', $user_base->user_id)
                        ->where('type', UserImage::TYPE_HEAD)
                        ->update(
                            [
                                'image_url' => ($params->get('head_img') ?: UserImage::defaultImage()),
                            ]
                        );
                }
                if (UserFinancial::getInitialize() > 0) {
                    $user_financial = new UserFinancial();
                    $user_financial->user_id = $user_base->user_id;
                    $user_financial->water_count = UserFinancial::getInitialize();
                    $user_financial->save();
                }
            } else {
                return [
                    'status' => false,
                    'message' => '系统一个人去旅行了，请稍后重试!',
                ];
            }
            $user = $user_base->where('user_id', $user_base->user_id)->IsOpen()->first()->toArray();
            if ($params->get('head_img')) {
                $user['user_head'] = $params->get('head_img');
            } else {
                $user['user_head'] = UserImage::defaultImage();
            }
            $user['token'] = TokenService::tokenEncode($user_base->user_id);
            UserLoginLog::insert_login_log($user_base->user_id);
            return $this->outputFormat(true, 'success', $this->formatUser($user));
        }
        //如果已经注册过
        $user = $user_base->where('user_id', $rt->user_id)->IsOpen()->first()->toArray();
        $image = $user_image->where('user_id', $rt->user_id)->head()->first();
        if ($image) {
            $user['user_head'] = $image->path();
        } else {
            $user['user_head'] = UserImage::defaultImage();
        }
        $user['token'] = TokenService::tokenEncode($rt->user_id);
        UserLoginLog::insert_login_log($rt->user_id);
        return $this->outputFormat(true, 'success', $this->formatUser($user));
    }
}
