<?php namespace App\Http\Controllers\Admin;

use App\Model\Activity;
use App\Model\ActivityFundraising;
use App\Model\ActivityImage;
use Illuminate\Http\Request;

class ActivityController extends BaseController
{
    public function add()
    {
        $this->title = '新增公益活动';
        $this->file_js = 'Admin/activity_add';
        return $this->view('admin.activity.add', ['choose_id' => 2]);
    }

    public function addSubmit(Request $request)
    {
        $title = $request->input('title');
        if (!$title) {
            return $this->returnAddJs('请填写标题！');
        }

        $url = $request->input('url');
        if (!$url) {
            return $this->returnAddJs('请填写活动链接！');
        }

        $description = $request->input('description');
        if (!$description) {
            return $this->returnAddJs('请填写描述！');
        }

        $statement = $request->input('statement');
        if (!$statement) {
            return $this->returnAddJs('请填写声明！');
        }

        $price = $request->input('price');
        if (!$price || !intval($price)) {
            return $this->returnAddJs('请填写捐赠额度！');
        }

        $video = $request->file('video');
        if (!$video || !$video->isValid()) {
            return $this->returnAddJs('视频文件无效！');
        }

        if (!($video = $this->updateFile($video))) {
            return $this->returnAddJs('视频上传失败，请重新上传！');
        }

        $image1 = $request->file('image1');
        if (!$image1 || !$image1->isValid()) {
            return $this->returnAddJs('请选择第一张图片！');
        }

        if (strpos($image1->getMimeType(), 'image/') === false) {
            return $this->returnAddJs('第一张图片格式不正确！');
        }

        if (!($image1 = $this->updateFile($image1))) {
            return $this->returnAddJs('第一张图片上传失败，请重新上传！');
        }

        $image2 = $request->file('image2');
        if (!$image2 || !$image2->isValid()) {
            return $this->returnAddJs('第二张图片无效！');
        }

        if (strpos($image2->getMimeType(), 'image/') === false) {
            return $this->returnAddJs('第二张图片格式不正确！');
        }

        if (!($image2 = $this->updateFile($image2))) {
            return $this->returnAddJs('第二张图片上传失败，请重新上传！');
        }

        $image3 = $request->file('image3');
        if (!$image3 || !$image3->isValid()) {
            return $this->returnAddJs('第三张图片无效');
        }

        if (strpos($image3->getMimeType(), 'image/') === false) {
            return $this->returnAddJs('第三张图片格式不正确');
        }

        if (!($image3 = $this->updateFile($image3))) {
            return $this->returnAddJs('第三张图片上传失败，请重新上传');
        }

        $activity = new Activity();
        $activity->title = $title;
        $activity->desc = $description;
        $activity->statement = $statement;
        $activity->url = $url;
        if ($activity->save()) {
            $images = [
                $image1,
                $image2,
                $image3,
            ];
            foreach ($images as $image) {
                $activity_image = new ActivityImage();
                $activity_image->activity_id = $activity->activity_id;
                $activity_image->image_url = $image;
                $activity_image->type = 1;
                $activity_image->save();
            }
            $activity_image = new ActivityImage();
            $activity_image->activity_id = $activity->activity_id;
            $activity_image->image_url = $video;
            $activity_image->type = 2;
            $activity_image->save();
            //插入捐赠额度
            $activity_fundraising = new ActivityFundraising();
            $activity_fundraising->activity_id = $activity->activity_id;
            $activity_fundraising->total_amount_price = $price;
            $activity_fundraising->save();
            return $this->returnAddJs('活动创建成功！');
        }
        return $this->returnAddJs('创建失败，请重新操作！');
    }

    /**
     * @param $file \Symfony\Component\HttpFoundation\File\UploadedFile|array
     * @return bool
     */
    private function updateFile($file)
    {
        $file_name = md5(microtime(true) . $file->getFileName()) . '.' . $file->getClientOriginalExtension();
        if ($file->move(getenv('FILE_PATH'), $file_name)) {
            return $file_name;
        }
        return false;
    }

    /**
     * @param $msg
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    private function returnAddJs($msg)
    {
        return response(
            "<script>alert('" . $msg . "');history.back(-1);</script>"
        );
    }
}