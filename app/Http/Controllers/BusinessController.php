<?php namespace App\Http\Controllers;

use App\Application\OrderService;
use Illuminate\Http\Request;
use App\Application\Controllers\BusinessService;
use \Response;

class BusinessController extends BaseController
{
    /**
     * 完善商户信息
     * @param Request $request
     * @return mixed
     */
    public function businessInfoFinish(Request $request)
    {
        $logo_image = $request->file('logoImage');
        $longitude = $request->get('longitude');
        $latitude = $request->get('latitude');
        $business_name = $request->get('business_name');
        $business_info = $request->get('business_info');
        $business_allow_image = $request->file('business_allowImage');
        $business_image = $request->file('business_image');
        $business_image2 = $request->file('business_image2');

        if (!$logo_image || !$longitude || !$latitude || !$business_name || !$business_info || !$business_allow_image || !$business_image) {
            return $this->fail('参数错误');
        }

        $logo_image_path = '';
        if (!empty($logo_image) && $logo_image->isValid()) {
            $logo_image_name = $this->updateFile($logo_image);
            if ($logo_image_name) {
                $logo_image_path = $logo_image_name;
            }
        }

        $business_allow_image_path = '';
        if (!empty($business_allow_image) && $business_allow_image->isValid()) {
            $business_allow_image_name = $this->updateFile($business_allow_image);
            if ($business_allow_image_name) {
                $business_allow_image_path = $business_allow_image_name;
            }
        }

        $business_image_path = '';
        if (!empty($business_image) && $business_image->isValid()) {
            $business_image_name = $this->updateFile($business_image);
            if ($business_image_name) {
                $business_image_path = $business_image_name;
            }
        }

        $business_image2_path = '';
        if (!empty($business_image2) && $business_image2->isValid()) {
            $business_image2_name = $this->updateFile($business_image2);
            if ($business_image2_name) {
                $business_image2_path = $business_image2_name;
            }
        }

        $check = (new BusinessService())->businessInfoFinish(
            $this->user_id,
            $logo_image_path,
            $longitude,
            $latitude,
            $business_name,
            $business_info,
            $business_allow_image_path,
            $business_image_path,
            $business_image2_path
        );

        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '获取成功！',
                    'businessInfo' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 获取商户信息
     * @return mixed
     */
    public function businessInfo()
    {
        $check = (new BusinessService())->businessInfo($this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '获取成功！',
                    'businessInfo' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 商户开始发送亲水包
     * @param Request $request
     * @return mixed
     */
    public function businessStart(Request $request)
    {
        $check = (new BusinessService())->businessStart($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '设置成功！',
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 商户暂停发送亲水包
     * @return mixed
     */
    public function businessEnd()
    {
        $check = (new BusinessService())->businessEnd($this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '设置成功！',
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 商户设置头像
     * @param Request $request
     * @return mixed
     */
    public function businessNewLogo(Request $request)
    {
        $head = $request->file('head');

        $logo_image_path = '';
        if (!empty($head) && $head->isValid()) {
            $logo_image_name = $this->updateFile($head);
            if ($logo_image_name) {
                $logo_image_path = $logo_image_name;
            }
        }

        $check = (new BusinessService())->businessNewLogo($logo_image_path, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '设置成功！',
                    'businessInfo' => $check['businessInfo']
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 商户设置新密码
     * @param Request $request
     * @return mixed
     */
    public function businessNewPassword(Request $request)
    {
        $check = (new BusinessService())->setBusinessNewPassword($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '设置成功！',
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 商户设置图片
     * @param Request $request
     * @return mixed
     */
    public function businessImage(Request $request)
    {
        $business_allow_image = $request->file('business_allowImage');
        $business_image = $request->file('business_image');
        $business_image2 = $request->file('business_image2');

        $business_allow_image_path = '';
        if (!empty($business_allow_image) && $business_allow_image->isValid()) {
            $business_allow_image_name = $this->updateFile($business_allow_image);
            if ($business_allow_image_name) {
                $business_allow_image_path = $business_allow_image_name;
            }
        }

        $business_image_path = '';
        if (!empty($business_image) && $business_image->isValid()) {
            $business_image_name = $this->updateFile($business_image);
            if ($business_image_name) {
                $business_image_path = $business_image_name;
            }
        }

        $business_image2_path = '';
        if (!empty($business_image2) && $business_image2->isValid()) {
            $business_image2_name = $this->updateFile($business_image2);
            if ($business_image2_name) {
                $business_image2_path = $business_image2_name;
            }
        }
        $check = (new BusinessService())->setBusinessNewImage(
            $this->user_id,
            $business_allow_image_path,
            $business_image_path,
            $business_image2_path
        );

        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '设置成功！',
                    'businessInfo' => $check['info']
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 商户设置简介
     * @param Request $request
     * @return mixed
     */
    public function businessNewInfo(Request $request)
    {
        $check = (new BusinessService())->setBusinessNewInfo($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '设置成功！',
                    'businessInfo' => $check['info']
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 商户意见反馈
     * @param Request $request
     * @return mixed
     */
    public function businessReport(Request $request)
    {
        $check = (new BusinessService())->setBusinessReport($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '提交成功！',
                    'businessInfo' => $check['info']
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 商户充值订单
     * @param Request $request
     * @return mixed
     */
    public function businessBankOrder(Request $request)
    {
        $check = (new OrderService())->businessBankOrder($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '提交成功！',
                    'businessInfo' => $check['info']
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 商户订单充值确认
     * @param Request $request
     * @return mixed
     */
    public function businessBankSure(Request $request)
    {
        $check = (new OrderService())->businessBankSure($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '提交成功！',
                    'businessInfo' => $check['info']
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 提现订单
     * @param Request $request
     * @return mixed
     */
    public function businessOutOrder(Request $request)
    {
        $check = (new OrderService())->businessOutOrder($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '提交成功！',
                    'businessInfo' => $check['info']
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 提现确认
     * @param Request $request
     * @return mixed
     */
    public function businessOutSure(Request $request)
    {
        $check = (new OrderService())->businessOutSure($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '提交成功！',
                    'businessInfo' => $check['info']
                ]
            );
        }
        return $this->fail($check['message']);
    }
}
