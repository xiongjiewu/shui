<?php namespace App\Application;

use App\Model\OrderLog;
use App\Model\UserBase;
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
        $money = $params->get('money');
        if (empty($money)) {
            return [
                'status' => false,
                'message' => '金额不能为空!',
                'info' => [],
            ];
        }

        $type = $params->get('type');
        if ($type === null) {
            return [
                'status' => false,
                'message' => '类型不能为空!',
                'info' => [],
            ];
        }

        /** @var \App\Model\OrderLog */
        $order_log = new OrderLog();
        $order_log->user_id = $user_id;
        $order_log->price = $money;
        $order_log->rate = $order_log::getRate();
        $order_log->water_count = ($order_log::getRate() * $money);
        $result = $order_log->save();
        if (empty($result)) {
            return [
                'status' => false,
                'message' => '生成失败!',
                'info' => [],
            ];
        }
        $order = [
            'order_id' => $order_log->id,
            'title' => OrderLog::getTitle($type),
            'body' => OrderLog::getDescribe($type),
        ];
        return [
            'status' => true,
            'message' => 'success',
            'info' => $order,
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
        $order_id = $params->get('orderID');
        if (empty($order_id)) {
            return [
                'status' => false,
                'message' => '订单ID不能为空!',
                'info' => [],
            ];
        }
        /** @var \App\Model\OrderLog */
        $order_log = new OrderLog();
        $result = $order_log->where('order_id', $order_id)->where('user_id', $user_id)->first();
        if ($result->status == OrderLog::STATUS_IS_TRUE) {
            return [
                'status' => false,
                'message' => '请勿重复充值!',
                'info' => [],
            ];
        }
        $user_financial = new UserFinancial();
        $user_financial_result = $user_financial->where('user_id', $user_id)->first();
        if (empty($user_financial_result)) {
            $user_financial->user_id = $user_id;
            $user_financial->water_count = $result->water_count;
            $user_financial->price = $result->price;
            $user_financial->save();
        } else {
            $user_financial->where('user_id', $user_id)->update(
                [
                    'water_count' => ($user_financial_result->water_count + $result->water_count),
                    'price' => ($user_financial_result->price + $result->price)
                ]
            );
        }
        $result = $order_log->where('order_id', $order_id)->update(['status' => OrderLog::STATUS_IS_TRUE]);
        if ($result) {
            return [
                'status' => true,
                'message' => '提交成功!',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'message' => '提交失败!',
                'info' => [],
            ];
        }
    }

    /**
     * 商户充值订单
     * @param $params
     * @param $user_id
     * @return mixed
     */
    public function businessBankOrder($params, $user_id)
    {
        $business_money = $params->get('business_money');
        if (empty($business_money)) {
            return [
                'status' => false,
                'message' => '金额不能为空!',
                'info' => [],
            ];
        }

        /** @var \App\Model\OrderLog */
        $order_log = new OrderLog();
        $order_log->user_id = $user_id;
        $order_log->price = $business_money;
        $order_log->rate = $order_log::getRate();
        $order_log->water_count = ($order_log::getRate() * $business_money);
        $result = $order_log->save();

        if (empty($result)) {
            return [
                'status' => false,
                'message' => '创建失败!',
                'info' => [],
            ];
        }
        return [
            'status' => true,
            'message' => 'success',
            'info' => ['order_id' => $order_log->id, 'order_info' => ''],
        ];
    }

    /**
     * 确认订单
     * @param $params
     * @param $user_id
     * @return array
     */
    public function businessBankSure($params, $user_id)
    {
        $order_id = $params->get('business_orderID');
        if (empty($order_id)) {
            return [
                'status' => false,
                'message' => '订单ID不能为空!',
                'info' => [],
            ];
        }
        /** @var \App\Model\OrderLog */
        $order_log = new OrderLog();
        $result = $order_log->where('order_id', $order_id)->where('user_id', $user_id)->first();
        if ($result->status == OrderLog::STATUS_IS_TRUE) {
            return [
                'status' => false,
                'message' => '请勿重复充值!',
                'info' => [],
            ];
        }
        $user_financial = new UserFinancial();
        $user_financial_result = $user_financial->where('user_id', $user_id)->first();
        if (empty($user_financial_result)) {
            $user_financial->user_id = $user_id;
            $user_financial->water_count = $result->water_count;
            $user_financial->price = $result->price;
            $user_financial->save();
        } else {
            $user_financial->where('user_id', $user_id)->update(
                [
                    'water_count' => ($user_financial_result->water_count + $result->water_count),
                    'price' => ($user_financial_result->price + $result->price)
                ]
            );
        }
        $result = $order_log->where('order_id', $order_id)->update(['status' => OrderLog::STATUS_IS_TRUE]);
        if ($result) {
            return [
                'status' => true,
                'message' => '提交成功!',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'message' => '提交失败!',
                'info' => [],
            ];
        }
    }

    /**
     * 提现订单
     * @param $params
     * @param $user_id
     * @return array
     */
    public function businessOutOrder($params, $user_id)
    {
        $business_money = $params->get('business_money');
        if (empty($business_money)) {
            return [
                'status' => false,
                'message' => '提现金额不能为空!',
                'info' => [],
            ];
        }

        /** @var \App\Model\OrderLog */
        $order_log = new OrderLog();
        $order_log->user_id = $user_id;
        $order_log->price = $business_money;
        $order_log->rate = $order_log::getRate();
        $order_log->water_count = ($order_log::getRate() * $business_money);
        $order_log->type = OrderLog::TYPE_SEND;
        $result = $order_log->save();

        if (empty($result)) {
            return [
                'status' => false,
                'message' => '创建失败!',
                'info' => [],
            ];
        }
        return [
            'status' => true,
            'message' => 'success',
            'info' => ['order_id' => $order_log->id, 'order_info' => ''],
        ];
    }

    /**
     * 提现确认
     * @param $params
     * @param $user_id
     * @return mixed
     */
    public function businessOutSure($params, $user_id)
    {
        $order_id = $params->get('orderID');
        if (empty($order_id)) {
            return [
                'status' => false,
                'message' => '订单ID不能为空!',
                'info' => [],
            ];
        }
        /** @var \App\Model\OrderLog */
        $order_log = new OrderLog();
        $result = $order_log->where('order_id', $order_id)->where('user_id', $user_id)->first();
        if ($result->status == OrderLog::STATUS_IS_TRUE) {
            return [
                'status' => false,
                'message' => '请勿重复提现!',
                'info' => [],
            ];
        }
        $user_financial = new UserFinancial();
        $user_financial_result = $user_financial->where('user_id', $user_id)->first();
        if (empty($user_financial_result)) {
            return [
                'status' => false,
                'message' => '您的金额为空，无法提现!',
                'info' => [],
            ];
        } else if ($user_financial_result->water_count < $result->water_count) {
            return [
                'status' => false,
                'message' => '您账户金额不够，无法提现!',
                'info' => [],
            ];
        } else {
            $user_financial->where('user_id', $user_id)->update(
                [
                    'water_count' => ($user_financial_result->water_count - $result->water_count),
                ]
            );
        }
        $result = $order_log->where('order_id', $order_id)->update(['status' => OrderLog::STATUS_IS_TRUE]);
        if ($result) {
            return [
                'status' => true,
                'message' => '提交成功!',
                'info' => [],
            ];
        } else {
            return [
                'status' => false,
                'message' => '提交失败!',
                'info' => [],
            ];
        }
    }


    /**
     * 用户护水值
     * @param $user_id
     * @return array
     */
    public function donations($user_id)
    {
        $user_financial = new UserFinancial();
        $user_financial_result = $user_financial->where('user_id', $user_id)->first();
        $data['protect_number'] = 0;
        $data['water_price'] = 0;
        if (!empty($user_financial_result)) {
            $data['protect_number'] = $user_financial_result->water_count;
            $data['water_price'] = $user_financial_result->water_count / OrderLog::getRate();
        }
        return [
            'status' => true,
            'message' => '反馈成功!',
            'info' => $data,
        ];
    }

    /**
     * 充值流水
     */
    public function getOrderList()
    {
        /** @var \App\Model\OrderLog */
        $order_log = new OrderLog();
        $order = $order_log->orderBy('order_id', 'desc')->paginate(20);
        $user_id = [];
        foreach ($order as $v) {
            $array = $v->toArray();
            array_push($user_id, $array['user_id']);
        }
        $user_list = [];
        if (!empty($user_id)) {
            $user_result = UserBase::whereIn('user_id', $user_id)->get()->toArray();
            if ($user_result) {
                foreach ($user_result as $u) {
                    $user_list[$u['user_id']] = $u['user_name'];
                }
            }
        }
        $list = [];
        foreach ($order as &$o) {
            $a = $o->toArray();
            $a['name'] = isset($user_list[$a['user_id']]) ? $user_list[$a['user_id']] : '不存在的用户';
            $list[] = $a;
            unset($a);
        }
        return [
            'list' => $list,
            'obj' => $order,
        ];
    }
}