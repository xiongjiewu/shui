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

        $code_arr = explode('-', $code);
        if (count($code_arr) != 2) {
            return response(['status' => 'error']);
        }
        $this->is_mobile = true;
        $this->show_title = false;
        $this->file_css = 'Share/code';
        $this->file_js = 'Share/code';
        return $this->view('activity.share', ['code' => $code_arr[1], 'share_code' => $code]);
    }

    /**
     * 接受手机号 验证码 分享批次-邀请码
     * @param Request $request
     * @return Response
     */
    public function shareGet(Request $request)
    {
//        return response([
//            'status' => false,
//            'code' => 4,
//            'message' => '您已经领取过',
//            'info' => [
//                'water_count' => 1232
//            ],
//        ]);
        return response((new ShareService())->shareGet($request));
    }

    /**
     * 展示领取的人
     * @param $code
     * @return mixed
     */
    public function shareShow($code)
    {
//        return response([
//            'status' => false,
//            'code' => 0,
//            'message' => '您已经领取过',
//            'userInfo' => [
//                [
//                    'user_name' => '小姐',
//                    'created_at' => '10.10 12:12',
//                    'share_water_count' => 121,
//                ],
//                [
//                    'user_name' => '小熊',
//                    'created_at' => '10.10 12:12',
//                    'share_water_count' => 4545,
//                ],
//                [
//                    'user_name' => '小熊',
//                    'created_at' => '10.10 12:12',
//                    'share_water_count' => 4545,
//                ]
//            ],
//        ]);
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