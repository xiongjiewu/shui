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
    const TYPE_BUSINESS = 2;//商户
    const TYPE_ADMIN = 3;//管理员

    public function scopeIsOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeUser($query)
    {
        return $query->where('type', self::TYPE_USER);
    }

    public function scopeBusiness($query)
    {
        return $query->where('type', self::TYPE_BUSINESS);
    }

    public function scopeAdmin($query)
    {
        return $query->where('type', self::TYPE_ADMIN);
    }

    public function isOpen()
    {
        return ($this->status == self::STATUS_OPEN);
    }
}
