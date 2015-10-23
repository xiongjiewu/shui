<?php namespace App\Console\Commands;

use App\Model\UserFinancial;
use App\Model\UserShareLog;
use Illuminate\Console\Command;

class RecyclingShareWater extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'recycling_share_water';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '回收分享的亲水值';

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
     * 回收分享的亲水值
     * Execute the console command.
     * @return mixed
     */
    public function fire()
    {
        $i = 0;
        while (true) {
            $user_share_water = new UserShareLog();
            $rt = $user_share_water->where('status', UserShareLog::SHARE_OK)
                ->where('id', '>', $i)->orderBy('id', 'asc')
                ->limit($this->limit)->get()->toArray();
            if (empty($rt)) {
                break;
            }
            foreach ($rt as $v) {
                if ((strtotime($v['created_at']) - time()) >= UserShareLog::getSystemTime()) {
                    $user_share_water->where('id', $v['id'])->update(
                        [
                            'status' => UserShareLog::SHARE_NO
                        ]
                    );
                    $user_ry = ($v['share_water_count'] - $v['share_receive']);
                    if ($user_ry > 0) {
                        $user_financial = new UserFinancial();
                        $user_rt = $user_financial->where('user_id', $v['user_id'])->first();
                        $user_financial->where('user_id', $v['user_id'])->update(
                            [
                                'water_count' => ($user_rt->water_count + $user_ry),
                                'send_water' => ($user_rt->send_water - $user_ry),
                            ]
                        );
                    }
                }
                $i = $v['id'];
            }
        }
    }
}