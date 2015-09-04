<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ActivityDonationsLog extends Model
{
    protected $table = 'activity_donations_log';

    //比率
    public static function getRate()
    {
        return getenv('RATE');
    }
}
