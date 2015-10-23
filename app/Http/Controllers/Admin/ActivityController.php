<?php namespace App\Http\Controllers\Admin;

use App\Application\ActivityService;
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
        return $this->view('admin.activity.add', ['choose_id' => 3]);
    }

    public function manage()
    {
        $this->title = '公益活动管理';
        $this->file_js = 'Admin/activity_manage';
        return $this->view('admin.activity.manage', ['choose_id' => 2, 'activities' => (new ActivityService())->getList()]);
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
        if (!$video->isValid()) {
            return $this->returnAddJs('视频文件无效！');
        }

        $video_w = $this->updateFile($video);

        if (!$video_w) {
            return $this->returnAddJs('视频上传失败，请重新上传！');
        }

        $image1 = $request->file('image1');
        if (!$image1->isValid()) {
            return $this->returnAddJs('请选择第一张图片！');
        }

        if (strpos($image1->getMimeType(), 'image/') === false) {
            return $this->returnAddJs('第一张图片格式不正确！');
        }

        $image1_w = $this->updateFile($image1);
        if (!$image1_w) {
            return $this->returnAddJs('第一张图片上传失败，请重新上传！');
        }

        $image2 = $request->file('image2');
        if (!$image2->isValid()) {
            return $this->returnAddJs('第二张图片无效！');
        }

        if (strpos($image2->getMimeType(), 'image/') === false) {
            return $this->returnAddJs('第二张图片格式不正确！');
        }

        $image2_w = $this->updateFile($image2);
        if (!$image2_w) {
            return $this->returnAddJs('第二张图片上传失败，请重新上传！');
        }

        $image3 = $request->file('image3');
        if (!$image3->isValid()) {
            return $this->returnAddJs('第三张图片无效');
        }

        if (strpos($image3->getMimeType(), 'image/') === false) {
            return $this->returnAddJs('第三张图片格式不正确');
        }

        $image3_w = $this->updateFile($image3);
        if (!$image3_w) {
            return $this->returnAddJs('第三张图片上传失败，请重新上传');
        }

        $activity = new Activity();
        $activity->title = $title;
        $activity->desc = $description;
        $activity->statement = $statement;
        $activity->url = $url;
        if ($activity->save()) {
            $images = [
                $image1_w,
                $image2_w,
                $image3_w,
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
            $activity_image->image_url = $video_w;
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
     * @param $msg
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    private function returnAddJs($msg)
    {
        return response(
            "<script>alert('" . $msg . "');history.back(-1);</script>"
        );
    }

    public function statusChange(Request $request)
    {
        if (!intval($request->get('id')) || !intval($request->get('status'))) {
            return response(
                [
                    'status' => false,
                    'message' => '请选择活动再进行操作！',
                ]
            );
        }

        if ((new ActivityService())->changeStatus($request->get('id'), ($request->get('status') == 1) ? 2 : 1)) {
            return response(
                [
                    'status' => true,
                    'message' => '操作成功！',
                ]
            );
        }
        return response(
            [
                'status' => true,
                'message' => '操作失败，请重试！',
            ]
        );
    }
}