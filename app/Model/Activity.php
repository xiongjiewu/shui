<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';

    const STATUS_OK = 1;
    const STATUS_NO = 2;

    public function scopeStatusOk($query)
    {
        return $query->where('status', self::STATUS_OK);
    }

    public function scopeStatusNo($query)
    {
        return $query->where('status', self::STATUS_NO);
    }
}
