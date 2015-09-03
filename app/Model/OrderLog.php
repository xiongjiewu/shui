<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    const STATUS_IS_TRUE = 1;
    const STATUS_IS_FALSE = 0;

    //比率
    const rate = 1;

    protected $table = 'order_log';
}
