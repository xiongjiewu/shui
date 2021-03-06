<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserImage extends Model
{
    protected $table = 'user_image';

    const IS_COMPLETION_QINIU = 2;    //来自七牛
    const IS_COMPLETION_TRUE = 1;   //来自微信
    const IS_COMPLETION_FALSE = 0;  //系统路径

    const TYPE_HEAD = 1;        //类型-头像LOGO
    const TYPE_BUSINESS = 2;    //类型-营业执照
    const TYPE_SHOP = 3;        //类型-店铺招牌 公司实景

    public static function getImagePath()
    {
        return getenv('THE_DOMAIN_NAME') . getenv('PIC_SHOW_PATH');
    }

    public static function getQiniuImagePath($path)
    {
        return getenv('QINIU_HOST') . '/' . $path;
    }

    /**
     * 获取文件完整路径
     * @return string
     */
    public function path()
    {
        if ($this->is_completion == self::IS_COMPLETION_FALSE) {
            return self::getImagePath() . '/' . $this->image_url;
        } else if ($this->is_completion == self::IS_COMPLETION_QINIU) {
            return getenv('QINIU_HOST') . '/' . $this->image_url;
        } else {
            return $this->image_url;
        }
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
