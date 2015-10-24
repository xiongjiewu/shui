<?php namespace App\Application;

use App\Model\UserBase;
use App\Model\UserFinancial;
use App\Model\UserImage;
use App\Model\UserRelationship;
use App\Model\UserSendWater;
use App\Model\UserShareLog;
use App\Model\UserShareReceiveLog;
use App\Model\UserVerify;
use Carbon\Carbon;
use \Queue;

class ShareService
{
    /**
     * 创建分享链接
     * @param $params
     * @param $user_id
     * @return array
     */
    public function createShareUrl($params, $user_id)
    {
        if (!$params->get('money')) {
            return [
                'status' => false,
                'message' => '分享亲水值不能为空!',
                'info' => [],
            ];
        }
        $user_financial = new UserFinancial();
        $user_financial_result = $user_financial->where('user_id', $user_id)->first();

        if (empty($user_financial_result)) {
            return [
                'status' => false,
                'message' => '对不起,您的亲水值为空!',
                'info' => [],
            ];
        }
        if ($user_financial_result->water_count < $params->get('money')) {
            return [
                'status' => false,
                'message' => '对不起,您的亲水值不够!',
                'info' => [],
            ];
        }

        $crc = crc32(md5(time() . $user_id));
        $user_shar_log = new UserShareLog();
        $user_shar_log->user_id = $user_id;
        $user_shar_log->share_water_count = $params->get('money');
        $user_shar_log->share_time = $crc;
        $user_shar_log->status = UserShareLog::SHARE_OK;

        if ($user_shar_log->save()) {
            $user_financial->where('user_id', $user_id)->update(
                [
                    'water_count' => ($user_financial_result->water_count - $params->get('money')),
                    'send_water' => ($user_financial_result->send_water + $params->get('money')),
                ]
            );
            $user_base = new UserBase();
            $user_base_rt = $user_base->where('user_id', $user_id)->first();
//            $time_out = Carbon::now()->addHour(getenv('TIMEOUT_HOUR'));
//            Queue::later($time_out, 'App\Queue\RecyclingWeixinShareWaterQueue', ['share_id' => $user_shar_log->id], 'send_water');
            return [
                'status' => true,
                'message' => '反馈成功!',
                'info' => [
                    'bag_id' => $crc,
                    'water_url' => UserShareLog::createShareUrl($crc, $user_base_rt->invite_code),
                ],
            ];
        } else {
            return [
                'status' => false,
                'message' => '对不起,系统一个人出去旅行了!',
                'info' => [],
            ];
        }
    }

    /**
     * 接受手机号 验证码 分享批次-邀请码
     * @param $params
     * @return array
     */
    public function shareGet($params)
    {
        $code = $params->get('code');
        $e = explode('-', $code);
        if (count($e) == 2) {
            $user_share_log = new UserShareLog();
            $user_share_log_tr = $user_share_log->where('share_time', $e[0])->first();
            if (empty($user_share_log_tr)) {
                return [
                    'status' => false,
                    'message' => '非法参数',
                    'code' => 1,
                    'info' => [],
                ];
            }
            if ($user_share_log_tr->status == UserShareLog::SHARE_NO) {
                return [
                    'status' => false,
                    'message' => '分享已经结束',
                    'code' => 2,
                    'info' => [],
                ];
            }

            $rt = $this->checkVerify($params->get('cellphone'), $params->get('verify'));
            if (!$rt['status']) {
                return [
                    'status' => false,
                    'code' => 3,
                    'message' => $rt['message'],
                    'userInfo' => [],
                ];
            }

            //注册成为用户
            $user_base = new UserBase();
            $user_base_rt = $user_base->where('user_cellphone', $params->get('cellphone'))->first();
            if (empty($user_base_rt)) {
                $user_id = $user_base->user_id;
                $password = mt_rand(100000, 999999);
                $user_base->user_cellphone = $params->get('cellphone');
                $user_base->password = $this->encryptPassword($password);
                $user_base->user_name = !empty($user_name) ? $user_name : '';
                $user_base->invite_code = crc32(md5($params->get('cellphone')));
                if ($user_base->save()) {
                    //发送短信提示
                    $user_verify = new UserVerify();
                    $content = '感谢你注册水想世界，您的APP登入密码为' . $password . '，请尽快登入之后修改密码噢～';
                    $user_verify->getSendMsgUrl($params->get('cellphone'), $content);
                    //建立图片
                    $user_image = new UserImage();
                    $user_image->user_id = $user_base->user_id;
                    $user_image->image_url = UserImage::defaultImage();
                    $user_image->type = UserImage::TYPE_HEAD;
                    $user_image->save();
                    //绑定关系
                    $user_relationship = new UserRelationship();
                    $user_relationship_rt = $user_relationship->where('user_id', $user_share_log_tr->user_id)
                        ->where('guest_id', $user_base->user_id)->first();
                    if (!empty($user_relationship_rt)) {
                        $user_relationship->user_id = $user_share_log_tr->user_id;
                        $user_relationship->guest_id = $user_base->user_id;
                        $user_relationship->save();
                    }
                }
            } else {
                $user_id = $user_base_rt->user_id;
            }

            $user_share_receive_log = new UserShareReceiveLog();
            $s = $user_share_receive_log->where('share_id', $user_share_log_tr->id)
                ->where('share_receive_user_id', $user_id)->first();
            if (!empty($s)) {
                return [
                    'status' => false,
                    'code' => 4,
                    'message' => '您已经领取过',
                    'info' => [
                        'water_count' => $s->share_water_count
                    ],
                ];
            }
            if (!empty($user_id)) {
                //开始分享
                $water = 0;
                $share_count = 0;
                $share_status = 1;
                switch ($user_share_log_tr->share_count) {
                    case 0:
                        $water = ceil($user_share_log_tr->share_water_count / 3);
                        $share_count = 1;
                        break;
                    case 1:
                        $water = ceil(($user_share_log_tr->share_water_count - $user_share_log_tr->share_receive) / 2);
                        $share_count = 2;
                        break;
                    case 2:
                        $water = $user_share_log_tr->share_water_count - $user_share_log_tr->share_receive;
                        $share_count = 3;
                        $share_status = 0;
                        break;
                }

                //放入用户账户
                $user_financial = new UserFinancial();
                $user_f_rt = $user_financial->where('user_id', $user_id)->first();
                if (empty($user_f_rt)) {
                    $user_financial->user_id = $user_id;
                    $user_financial->water_count = $water;
                    $user_financial->save();
                } else {
                    $user_financial->where('user_id', $user_id)->update(
                        [
                            'water_count' => ($user_f_rt->water_count + $water),
                        ]
                    );
                }

                $user_share_log->where('id', $user_share_log_tr->id)->update(
                    [
                        'share_count' => $share_count,
                        'status' => $share_status,
                        'share_receive' => $user_share_log_tr->share_receive + $water,
                    ]
                );

                //建立领取记录
                $user_send_water = new UserSendWater();
                $user_send_water->user_id = $user_share_log_tr->user_id;
                $user_send_water->water_count = $water;
                $user_send_water->accept_user_id = $user_id;
                $user_send_water->overdue_date = time();
                $user_send_water->share_type = UserSendWater::SHARE_TYPE_WEIXIN;
                $user_send_water->status = UserSendWater::STATUS_IS_TRUE;
                $user_send_water->save();

                //记录每期领取的人
                $user_share_receive_log->share_id = $user_share_log_tr->id;
                $user_share_receive_log->share_receive_user_id = $user_id;
                $user_share_receive_log->share_water_count = $water;
                $user_share_receive_log->save();

                return [
                    'status' => true,
                    'message' => '领取成功',
                    'info' => [
                        'water_count' => $water
                    ],
                ];
            } else {
                return [
                    'status' => false,
                    'code' => 5,
                    'message' => '系统一个人旅行去了，请重试!',
                    'info' => [],
                ];
            }
        } else {
            return [
                'status' => false,
                'code' => 1,
                'message' => '非法参数',
                'info' => [],
            ];
        }
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
     * 展示领取的信息
     * @param $code
     * @return array
     */
    public function shareShow($code)
    {
        $e = explode('-', $code);
        if (count($e) == 2) {
            $user_share_log = new UserShareLog();
            $user_share_log_tr = $user_share_log->where('share_time', $e[0])->first();
            $user_share_receive_log = new UserShareReceiveLog();
            $u_s_r_l = $user_share_receive_log->where('share_id', $user_share_log_tr->id)->get()->toArray();
            $user_ids = [];
            foreach ($u_s_r_l as $v) {
                array_push($user_ids, $v['share_receive_user_id']);
            }
            $user_base = new UserBase();
            $user_rt = $user_base->whereIn('user_id', $user_ids)->get()->toArray();
            foreach ($u_s_r_l as &$r) {
                $r['created_at'] = date('m.d H:s', strtotime($r['created_at']));
                foreach ($user_rt as $u) {
                    if ($r['share_receive_user_id'] == $u['user_id']) {
                        $r['user_name'] = !empty($u['user_name']) ? $u['user_name'] : '匿名';
                    }
                }
            }
            return [
                'status' => true,
                'message' => '返回成功',
                'info' => $u_s_r_l,
            ];
        } else {
            return [
                'status' => false,
                'message' => '非法参数',
                'info' => [],
            ];
        }
    }
}