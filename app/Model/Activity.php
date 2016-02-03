<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';

    protected $primaryKey = 'activity_id';

    const STATUS_OK = 1;
    const STATUS_NO = 2;

    const _GONGYI = 1;      //来自公益
    const _QINSHUIQUAN = 2; //来自亲水圈

    public function scopeStatusOk($query)
    {
        return $query->where('status', self::STATUS_OK);
    }

    public function scopeStatusNo($query)
    {
        return $query->where('status', self::STATUS_NO);
    }

    public function scopeGongyi($query)
    {
        return $query->where('status', self::_GONGYI);
    }

    public function scopeQingshuiquan($query)
    {
        return $query->where('status', self::_QINSHUIQUAN);
    }
}
