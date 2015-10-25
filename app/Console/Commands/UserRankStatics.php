<?php namespace App\Console\Commands;

use App\Model\UserBase;
use App\Model\UserFinancial;
use App\Model\UserRank;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UserRankStatics extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user_rank_statics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算用户排名';

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
        $rank = UserRank::getDefaultRank();
        while (true) {
            $user_financial = new UserFinancial();
            $user_financial_result = $user_financial->where('user_id', '>', $i)
                ->limit($this->limit)
                ->orderBy('water_count', 'desc')
                ->get()->toArray();
            if (empty($user_financial_result)) {
                break;
            }
            $user_base = new UserBase();
            foreach ($user_financial_result as $v) {
                $r = $user_base->where('user_id', $v['user_id'])->first();
                if ($r['type'] == UserBase::TYPE_USER) {
                    $user_rank = new UserRank();
                    $rt = $user_rank->where('user_id', $v['user_id'])->where('date', Carbon::now()->format('Ymd'))->first();
                    if (empty($rt)) {
                        $user_rank->user_id = $v['user_id'];
                        $user_rank->rank = $rank;
                        $user_rank->date = Carbon::now()->format('Ymd');
                        $user_rank->save();
                        $rank += 1;
                    }
                }
                $i = $v['user_id'];
            }
        }
    }
}