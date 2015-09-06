<?php namespace App\Jobs;

use App\Model\UserBase;
use App\Model\UserBlackWater;
use App\Model\UserLoginLog;

class CalculateTheBlackWater extends Job
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'calculate_the_black_water';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算黑水值';

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
     * 计算黑水值
     * Execute the console command.
     * @return mixed
     */
    public function fire()
    {
        $i = 0;
        $black_water_val = (int)getenv('BLACK_WATER');
        while (true) {
            $result = UserBase::where('user_id', '>', $i)->limit($this->limit)->get()->toArray();
            $i += $this->limit;
            foreach ($result as $value) {
                $user_login_log = UserLoginLog::where('user_id', $value['user_id'])->where('date', date('Ymd', strtotime('-1 day')))->first();
                if (empty($user_login_log)) {
                    $user_black_water = new UserBlackWater();
                    $user_black_water_result = $user_black_water->where('user_id', $value['user_id'])->first();
                    if (empty($user_black_water_result)) {
                        $user_black_water->user_id = $value['user_id'];
                        $user_black_water->black_water = $black_water_val;
                        $user_black_water->save();
                    } else {
                        $user_black_water->where('user_id', $value['user_id'])->update(
                            [
                                'black_water' => ($user_black_water_result->black_water + $black_water_val)
                            ]
                        );
                    }
                }
            }
        }
    }
}