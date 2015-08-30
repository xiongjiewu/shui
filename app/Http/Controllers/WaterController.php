<?php namespace App\Http\Controllers;

use App\Application\WaterService;
use Input;
use \Response;

class WaterController extends BaseController
{
    /**
     * 水地图详情
     * @return array
     */
    public function mapDetail()
    {
        $params = Input::All();
        $check = (new WaterService())->mapDetail($params);
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
     * @return mixed
     */
    public function mapList()
    {
        $params = Input::All();
        $check = (new WaterService())->mapList($params);
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
}