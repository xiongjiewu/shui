<?php namespace App\Application;

use App\Application\User\TokenService;
use App\Model\Report;
use App\Model\UserBase;
use App\Model\UserCompanyExtend;
use App\Model\UserFinancial;
use App\Model\UserImage;

class BusinessService
{
    /**
     * 存入商户信息
     * @param $user_id
     * @param $logo_image_path
     * @param $longitude
     * @param $latitude
     * @param $business_name
     * @param $business_info
     * @param $business_allow_image
     * @param $business_image
     * @param $business_image2
     * @param $business_address
     * @return array
     */
    public function businessInfoFinish(
        $user_id,
        $logo_image_path,
        $longitude,
        $latitude,
        $business_name,
        $business_info,
        $business_allow_image,
        $business_image,
        $business_image2,
        $business_address
    )
    {
        $user_company_extend = new UserCompanyExtend();
        $user_company_extend->user_id = $user_id;
        $user_company_extend->user_company_name = $business_name;
        $user_company_extend->user_address = $business_address;
        $user_company_extend->user_desc = $business_info;
        $user_company_extend->user_company_lat = $latitude;
        $user_company_extend->user_company_lng = $longitude;
        $user_company_extend->save();


        if ($logo_image_path) {
            $user_image_one = new UserImage();
            $user_image_one->user_id = $user_id;
            $user_image_one->image_url = $logo_image_path;
            $user_image_one->is_completion = UserImage::IS_COMPLETION_QINIU;
            $user_image_one->type = UserImage::TYPE_HEAD;
            $user_image_one->save();
            unset($user_image_one);
        }

        if ($business_allow_image) {
            $user_image_two = new UserImage();
            $user_image_two->user_id = $user_id;
            $user_image_two->image_url = $business_allow_image;
            $user_image_two->is_completion = UserImage::IS_COMPLETION_QINIU;
            $user_image_two->type = UserImage::TYPE_BUSINESS;
            $user_image_two->save();
            unset($user_image_two);
        }

        if ($business_image) {
            $user_image_thr = new UserImage();
            $user_image_thr->user_id = $user_id;
            $user_image_thr->image_url = $business_image;
            $user_image_thr->is_completion = UserImage::IS_COMPLETION_QINIU;
            $user_image_thr->type = UserImage::TYPE_SHOP;
            $user_image_thr->save();
            unset($user_image_thr);
        }

        if ($business_image2) {
            $user_image_fou = new UserImage();
            $user_image_fou->user_id = $user_id;
            $user_image_fou->image_url = $business_image2;
            $user_image_fou->is_completion = UserImage::IS_COMPLETION_QINIU;
            $user_image_fou->type = UserImage::TYPE_SHOP;
            $user_image_fou->save();
            unset($user_image_fou);
        }

        return [
            'status' => true,
            'message' => 'success',
            'info' => [],
        ];

    }

    /**
     * 获取商户信息
     * @param $user_id
     * @return array
     */
    public function getBusinessInfo($user_id)
    {
        $user_base = new UserBase();
        $user_result = $user_base->where('user_id', $user_id)->first();

        $user_company_extend = new UserCompanyExtend();
        $company_result = $user_company_extend->where('user_id', $user_id)->first();

        $info = [];
        $info['business_id'] = $user_id;
        $info['business_cellphone'] = $user_result->user_cellphone;
        $info['business_longitude'] = '';
        $info['business_latitude'] = '';
        $info['business_name'] = '';
        $info['business_info'] = '';

        if (!empty($company_result)) {
            $info['business_longitude'] = $company_result->user_company_lng;
            $info['business_latitude'] = $company_result->user_company_lat;
            $info['business_name'] = $company_result->user_company_name;
            $info['business_info'] = $company_result->user_desc;
        }

        $user_image = new UserImage();
        $user_image_result = $user_image->where('user_id', $user_id)->get();

        $info['business_logo'] = '';
        $info['business_allowImage'] = '';
        $info['business_image'] = '';
        $info['business_image2'] = '';

        foreach ($user_image_result as $user_image_result_v) {
            if ($user_image_result_v->type == UserImage::TYPE_HEAD) {
                $info['business_logo'] = $user_image_result_v->path();
            }
            if ($user_image_result_v->type == UserImage::TYPE_BUSINESS) {
                $info['business_allowImage'] = $user_image_result_v->path();
            }
            if ($user_image_result_v->type == UserImage::TYPE_SHOP) {
                if (empty($info['business_image'])) {
                    $info['business_image'] = $user_image_result_v->path();
                } else {
                    $info['business_image2'] = $user_image_result_v->path();
                }
            }
        }
        return $info;
    }

    /**
     * 获取商户详情信息
     * @param $user_id
     * @return array
     */
    public function businessInfo($user_id)
    {
        $info = $this->getBusinessInfo($user_id);

        return [
            'status' => true,
            'message' => 'success',
            'info' => $info,
        ];
    }

    /**
     * 商户开始发送亲水包
     * @param $params
     * @param $user_id
     * @return array
     */
    public function businessStart($params, $user_id)
    {
        $water_num = $params->get('water_num');
        if (empty($water_num)) {
            return [
                'status' => false,
                'message' => '你的亲水值不够!',
                'info' => [],
            ];
        }
        $user_financial = new UserFinancial();
        $result = $user_financial->where('user_id', $user_id)->first();
        if (!empty($result)) {
            $bool = $user_financial->where('user_id', $user_id)->update(
                [
                    'giving' => $water_num
                ]
            );
        } else {
            $user_financial->user_id = $user_id;
            $user_financial->giving = $water_num;
            $bool = $user_financial->save();
        }
        if ($bool) {
            return [
                'status' => true,
                'message' => 'success',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'message' => '设置失败!',
                'info' => [],
            ];
        }
    }

    /**
     * 商户暂停发送亲水包
     * @param $user_id
     * @return array
     */
    public function businessEnd($user_id)
    {
        $user_financial = new UserFinancial();
        $result = $user_financial->where('user_id', $user_id)->first();
        if (!empty($result)) {
            $bool = $user_financial->where('user_id', $user_id)->update(['giving' => UserFinancial::DEFAULT_GIVING]);
        } else {
            $user_financial->user_id = $user_id;
            $user_financial->giving = UserFinancial::DEFAULT_GIVING;
            $bool = $user_financial->save();
        }
        if ($bool) {
            return [
                'status' => true,
                'message' => 'success',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'message' => '设置失败!',
                'info' => [],
            ];
        }
    }

    /**
     * 商户设置头像
     * @param $logo_image_path
     * @param $user_id
     * @return array
     */
    public function businessNewLogo($logo_image_path, $user_id)
    {
        if (empty($logo_image_path)) {
            return [
                'status' => false,
                'message' => '您的头像不能为空!',
                'info' => [],
            ];
        }
        $user_image = new UserImage();
        $rt = $user_image->where('user_id', $user_id)->Head()->first();
        if (!empty($rt)) {
            $bool = $user_image->where('user_id', $user_id)->Head()
                ->update(
                    [
                        'image_url' => $logo_image_path
                    ]
                );
        } else {
            $user_image->user_id = $user_id;
            $user_image->image_url = $logo_image_path;
            $user_image->type = UserImage::TYPE_HEAD;
            $user_image->is_completion = UserImage::IS_COMPLETION_QINIU;
            $bool = $user_image->save();
        }
        if ($bool) {
            $info['business_logo'] = UserImage::getQiniuImagePath($logo_image_path);
            $info['business_id'] = TokenService::tokenEncode($user_id);
            return [
                'status' => true,
                'message' => 'success',
                'info' => $info,
            ];
        } else {
            return [
                'status' => false,
                'message' => '修改成功!',
                'info' => [],
            ];
        }
    }

    /**
     * 为商户设置新密码
     * @param $params
     * @param $user_id
     * @return array
     */
    public function setBusinessNewPassword($params, $user_id)
    {
        $new_password = $params->get('newPassword');
        if (trim($new_password) == '') {
            return [
                'status' => false,
                'message' => '你的新密码不能为空!',
                'info' => [],
            ];
        }
        $result = UserBase::where('user_id', $user_id)->update(
            [
                'password' => $this->encryptPassword($new_password)
            ]
        );
        if ($result) {
            return [
                'status' => true,
                'message' => 'success',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'message' => '修改失败!',
                'info' => [],
            ];
        }
    }

    /**
     * 为商户设置新图片
     * @param $user_id
     * @param $business_allow_image_path
     * @param $business_image_path
     * @param $business_image2_path
     * @return array
     */
    public function setBusinessNewImage(
        $user_id,
        $business_allow_image_path,
        $business_image_path,
        $business_image2_path
    )
    {
        if (!empty($business_allow_image_path)) {
            $user_image_model_o = new UserImage();
            $user_image_model_o->user_id = $user_id;
            $user_image_model_o->type = UserImage::TYPE_BUSINESS;
            $user_image_model_o->image_url = $business_allow_image_path;
            $user_image_model_o->save();
            unset($user_image_model_o);
        }

        if (!empty($business_image_path)) {
            $user_image_model_t = new UserImage();
            $user_image_model_t->user_id = $user_id;
            $user_image_model_t->type = UserImage::TYPE_SHOP;
            $user_image_model_t->image_url = $business_image_path;
            $user_image_model_t->save();
            unset($user_image_model_t);
        }

        if (!empty($business_image2_path)) {
            $user_image_model_f = new UserImage();
            $user_image_model_f->user_id = $user_id;
            $user_image_model_f->type = UserImage::TYPE_SHOP;
            $user_image_model_f->image_url = $business_image2_path;
            $user_image_model_f->save();
            unset($user_image_model_f);
        }

        $data = [];
        $data['business_id'] = $user_id;
        $user_image_model = new UserImage();
        $result = $user_image_model->where('user_id', $user_id)->get();
        foreach ($result as $value) {
            if ($value['type'] == UserImage::TYPE_BUSINESS) {
                $data['business_allowImage'] = $value->path();
            }
            if ($value['type'] == UserImage::TYPE_SHOP) {
                if (empty($data['business_image'])) {
                    $data['business_image'] = $value->path();
                } else {
                    $data['business_image2'] = $value->path();
                }
            }
        }

        return [
            'status' => true,
            'message' => 'success',
            'info' => $data,
        ];
    }

    /**
     * 商户设置简介
     * @param $params
     * @param $user_id
     * @return mixed
     */
    public function setBusinessNewInfo($params, $user_id)
    {
        $business_info = $params->get('businessInfo');
        if (trim($business_info) == '') {
            return [
                'status' => false,
                'message' => '简介不能为空!',
                'info' => [],
            ];
        }
        $result = UserCompanyExtend::where('user_id', $user_id)->update(
            [
                'user_desc' => $business_info
            ]
        );
        if ($result) {
            return [
                'status' => true,
                'message' => 'success',
                'info' => [
                    'business_id' => $user_id,
                    'business_info' => $business_info,
                ],
            ];
        } else {
            return [
                'status' => false,
                'message' => '修改失败!',
                'info' => [],
            ];
        }
    }

    /**
     * 商户意见反馈
     * @param $params
     * @param $user_id
     * @return mixed
     */
    public function setBusinessReport($params, $user_id)
    {
        $report = $params->get('businessReport');
        if (trim($report) == '') {
            return [
                'status' => false,
                'message' => '举报内容不能为空!',
                'info' => [],
            ];
        }
        $report = new Report();
        $report->user_id = $user_id;
        $report->report = $report;
        $report->type = Report::TYPE_BUSINESS;
        $result = $report->save();
        if ($result) {
            return [
                'status' => true,
                'message' => 'success',
                'info' => [
                    'business_id' => $user_id,
                    'business_report' => $report,
                ],
            ];
        } else {
            return [
                'status' => false,
                'message' => '修改失败!',
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
}