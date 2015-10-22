<?php namespace App\Http\Controllers;

use App\Application\ShareService;
use App\Http\Controllers\Admin\BaseController as AdminBaseController;
use \Illuminate\Http\Request;
use \Response;

class ShareController extends AdminBaseController
{
    public function shareCode($code)
    {
        if (!$code) {
            return response(['status' => 'error']);
        }

        $this->is_mobile = true;
        $this->show_title = false;
        $this->file_css = 'Share/code';
        $this->file_js = 'Share/code';
        return $this->view('activity.share', ['code' => $code]);
    }

    /**
     * 接受手机号 验证码 分享批次-邀请码
     * @param Request $request
     */
    public function shareGet(Request $request)
    {
        $check = (new ShareService())->shareGet($request);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '接受成功！',
                    'userInfo' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 展示领取的人
     * @param $code
     * @return mixed
     */
    public function shareShow($code)
    {
        $check = (new ShareService())->shareShow($code);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '接受成功！',
                    'userInfo' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }
}