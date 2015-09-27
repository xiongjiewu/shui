<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    const TYPE_USER = 1; //用户反馈
    const TYPE_BUSINESS = 2; //商户反馈

    protected $table = 'report';
}
