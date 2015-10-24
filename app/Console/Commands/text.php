<?php namespace App\Console\Commands;

use App\Model\GetShopWaterLog;
use Illuminate\Console\Command;

class text extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'text';

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
        $e = new GetShopWaterLog();
        $e->user_id = 999999;
        $e->water_count = 9999999;
        $e->giving_user_id = 9999999;
        $e->save();
    }
}