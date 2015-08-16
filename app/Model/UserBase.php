<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserBase extends Model
{
    protected $table = 'user_base';

    protected $primaryKey = 'user_id';

    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;

    const TYPE_USER = 1;//用户
    const TYPE_BUSINESS = 2;//用户

    public function scopeIsOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }
}
