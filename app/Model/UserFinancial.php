<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserFinancial extends Model
{
    const DEFAULT_GIVING = 0;

    protected $table = 'user_financial';

    /**
     * 初始化积分
     * @return string
     */
    public static function getInitialize()
    {
        return getenv('USER_INITIALIZE');
    }
}