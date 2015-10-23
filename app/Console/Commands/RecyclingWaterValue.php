<?php namespace App\Console\Commands;

use App\Model\UserFinancial;
use App\Model\UserSendWater;
use Illuminate\Console\Command;

class RecyclingWaterValue extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'recycling_water_value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '回收亲水值';

    /**
     * limit
     * @var int
     */
    private $limit = 100;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 回收亲水值
     * Execute the console command.
     * @return mixed
     */
    public function fire()
    {
        $i = 0;
        while (true) {
            $user_send_water = new UserSendWater();
            $user_send_rt = $user_send_water->where('status', UserSendWater::STATUS_IS_FALSE)
                ->where('id', '>', $i)->limit($this->limit)->orderBy('id', 'asc')->get()->toArray();
            if (empty($user_send_rt)) {
                break;
            }
            $user_financial = new UserFinancial();
            foreach ($user_send_rt as $user_send_rt_v) {
                //如果创建时间比现在超过48小时则算无效
                if ((strtotime($user_send_rt_v['created_at']) - time()) >= UserSendWater::getSystemTime()) {
                    $result = $user_send_water->where('id', $user_send_rt['id'])->first();
                    if (!empty($result) && $result->status == UserSendWater::STATUS_IS_FALSE) {
                        $user_send_water->where('id', $user_send_rt['id'])->update(
                            [
                                'status' => UserSendWater::STATUS_IS_ACTIVE_FALSE,
                            ]
                        );
                        $user_financial_result = $user_financial->where('user_id', $result->user_id)->first();
                        $user_financial->where('user_id', $result->user_id)->update(
                            [
                                'water_count' => ($user_financial_result->water_count + $result->water_count),
                                'send_water' => ($user_financial_result->send_water - $result->water_count)
                            ]
                        );
                    }
                }
                $i = $user_send_rt['id'];
            }
        }
    }
}