<?php namespace App\Http\Controllers;

use App\Application\WaterService;
use \Illuminate\Http\Request;
use \Response;

class WaterController extends BaseController
{
    /**
     * 水地图详情
     * @param Request $request
     * @return mixed
     */
    public function mapDetail(Request $request)
    {
        $check = (new WaterService())->mapDetail($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '获取成功！',
                    'store_detail' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 水地图列表
     * @param Request $request
     * @return mixed
     */
    public function mapList(Request $request)
    {
        $check = (new WaterService())->mapList($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '获取成功！',
                    'store_detail' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }

    /**
     * 水地图亲水包获取
     * @param Request $request
     * @return mixed
     */
    public function mapBag(Request $request)
    {
        $check = (new WaterService())->mapBag($request, $this->user_id);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '获取成功！',
                    'get_Info' => $check['info'],
                ]
            );
        }
        return $this->fail($check['message']);
    }
}