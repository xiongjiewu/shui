<?php namespace App\Application;

use App\Model\OrderLog;
use App\Model\UserFinancial;

class OrderService
{
    /**
     * 创建订单
     * @param $params
     * @param $user_id
     * @return array
     */
    public function bankOrder($params, $user_id)
    {
        /** @var \App\Model\OrderLog */
        $order_log = new OrderLog();
        $order_log->user_id = $user_id;
        $order_log->price = $params->get('money');
        $order_log->rate = OrderLog::rate;
        $order_log->water_count = (OrderLog::rate * $params->get('money'));
        $result = $order_log->save();
        if (!empty($result)) {
            return [
                'status' => false,
                'msg' => '生成失败!',
                'info' => [],
            ];
        }
        return [
            'status' => true,
            'msg' => 'success',
            'info' => ['order_id' => $result->id, 'order_info' => ''],
        ];
    }

    /**
     * 确认订单
     * @param $params
     * @param $user_id
     * @return array
     */
    public function bankSure($params, $user_id)
    {
        /** @var \App\Model\OrderLog */
        $order_log = new OrderLog();
        $result = $order_log->where('order_id', $params->get('orderID'))->where('user_id', $user_id)->first();
        $user_financial = new UserFinancial();
        $user_financial_result = $user_financial->where('user_id', $user_id)->first();
        if (empty($user_financial_result)) {
            $user_financial_result->user_id = $user_id;
            $user_financial_result->water_count = $result->water_count;
            $user_financial_result->price = $result->price;
            $user_financial_result->save();
        } else {
            $user_financial_result->where('user_id', $user_id)->update(
                [
                    'water_count' => ($user_financial_result->water_count + $result->water_count),
                    'price' => ($user_financial_result->price + $result->price)
                ]
            );
        }
        $result = $order_log->where('order_id', $params->get('orderID'))->update(['status' => OrderLog::STATUS_IS_TRUE]);
        if ($result) {
            return [
                'status' => true,
                'msg' => '提交成功!',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'msg' => '提交失败!',
                'info' => [],
            ];
        }
    }

}