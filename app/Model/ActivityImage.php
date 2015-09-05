<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ActivityImage extends Model
{
    protected $table = 'activity_image';

    const TYPE_IMAGE_IS_PIC = 1;
    const TYPE_IMAGE_IS_GIF = 2;

    const IMAGE_PATH = '/data/activity_image';//图片/视频存放路径

    /**
     * 获取文件完整路径
     * @return string
     */
    public function path()
    {
        return self::IMAGE_PATH . '/' . $this->image_url;
    }

    public function scopePIC($query)
    {
        return $query->where('status', self::TYPE_IMAGE_IS_PIC);
    }

    public function scopeGIF($query)
    {
        return $query->where('status', self::TYPE_IMAGE_IS_GIF);
    }

    public static function defaultImage()
    {
        return '';
    }

    public static function getImages($activity_id, $type = self::TYPE_IMAGE_IS_PIC)
    {
        $result = self::where('activity_id', $activity_id)->where('type', $type)->get()->toArray();
        return (count($result) == 1) ? array_shift($result) : $result;
    }
}
