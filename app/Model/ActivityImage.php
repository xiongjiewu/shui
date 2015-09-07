<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ActivityImage extends Model
{
    protected $table = 'activity_image';

    const TYPE_IMAGE_IS_PIC = 1;
    const TYPE_IMAGE_IS_GIF = 2;

    /**
     * 获取文件完整路径
     * @return string
     */
    public function path()
    {
        return getenv('FILE_PATH') . '/' . $this->image_url;
    }

    public function scopePIC($query)
    {
        return $query->where('type', self::TYPE_IMAGE_IS_PIC);
    }

    public function scopeGIF($query)
    {
        return $query->where('type', self::TYPE_IMAGE_IS_GIF);
    }

    public static function defaultImage()
    {
        return '';
    }

    public static function getImages($activity_id, $type = self::TYPE_IMAGE_IS_PIC)
    {
        $result = self::where('activity_id', $activity_id)->where('type', $type)->get()->toArray();
        if (empty($result)) {
            return (Object)[];
        }
        return (count($result) == 1) ? array_shift($result) : $result;
    }
}
