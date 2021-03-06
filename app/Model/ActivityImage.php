<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ActivityImage extends Model
{
    protected $table = 'activity_image';

    const TYPE_IMAGE_IS_PIC = 1;
    const TYPE_IMAGE_IS_GIF = 2;

    const COMPLETE_PATH = 1;    //完整路径
    const RELATIVE_PATH = 0;    //相对路径

    public static function getImagePath()
    {
        return getenv('THE_DOMAIN_NAME') . getenv('PIC_SHOW_PATH');
    }

    /**
     * 获取文件完整路径
     * @return string
     */
    public function path()
    {
        if ($this->is_completion == self::RELATIVE_PATH) {
            return self::getImagePath() . '/' . $this->image_url;
        } else {
            return $this->image_url;
        }
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
        foreach ($result as &$value) {
            if ($value['is_completion'] == self::RELATIVE_PATH) {
                $value['image_url'] = self::getImagePath() . '/' . $value['image_url'];
            }
        }
        return (count($result) == 1) ? array_shift($result) : $result;
    }
}
