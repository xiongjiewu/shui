<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserImage extends Model
{
    protected $table = 'user_image';

    const TYPE_HEAD = 1;//类型-头像
    const TYPE_BUSINESS = 2;//类型-营业执照
    const TYPE_SHOP = 3;//类型-店铺招牌

    /**
     * 获取文件完整路径
     * @return string
     */
    public function path()
    {
        return parent::getImagePath() . '/' . $this->image_url;
    }

    /**
     * 拼接头像query
     * @param $query
     * @return mixed
     */
    public function scopeHead($query)
    {
        return $query->where('type', self::TYPE_HEAD);
    }

    /**
     * 拼接营业执照query
     * @param $query
     * @return mixed
     */
    public function scopeBusiness($query)
    {
        return $query->where('type', self::TYPE_BUSINESS);
    }

    /**
     * 拼接店铺招牌query
     * @param $query
     * @return mixed
     */
    public function scopeShop($query)
    {
        return $query->where('type', self::TYPE_SHOP);
    }

    public static function defaultImage()
    {
        return '';
    }
}
