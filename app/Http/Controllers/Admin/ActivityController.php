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
        return $this->view('admin.activity.add', ['choose_id' => 3, 'action' => '新增']);
    }

    public function edit($activity_id)
    {
        $this->title = '修改公益活动';
        $this->file_js = 'Admin/activity_add';
        $activity_rt = Activity::where('activity_id', $activity_id)->first();
        $activity_fundraising_rt = ActivityFundraising::where('activity_id', $activity_id)->first();
        $activity_image_rt = ActivityImage::where('activity_id', $activity_id)->get();
        $video_url = '';
        $video_id = '';
        $image1_id = '';
        $image2_id = '';
        $image3_id = '';
        foreach ($activity_image_rt as $activity_image_rt_v) {
            if ($activity_image_rt_v->type == ActivityImage::TYPE_IMAGE_IS_GIF) {
                $video_url = $activity_image_rt_v->image_url;
                $video_id = $activity_image_rt_v->id;
            } else if (empty($image1_id)) {
                $image1_id = $activity_image_rt_v->id;
            } else if (empty($image2_id)) {
                $image2_id = $activity_image_rt_v->id;
            } else if (empty($image3_id)) {
                $image3_id = $activity_image_rt_v->id;
            }

        }
        return $this->view('admin.activity.add', [
            'choose_id' => 3,
            'action' => '修改',
            'activity_id' => $activity_id,
            'base_activity' => $activity_rt->toArray(),
            'base_activity_fundraising' => $activity_fundraising_rt->toArray(),
            'video_url' => $video_url,
            'video_id' => $video_id,
            'image1_id' => $image1_id,
            'image2_id' => $image2_id,
            'image3_id' => $image3_id,
        ]);
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

        $video_w = $request->input('video');
        if (!$video_w) {
            return $this->returnAddJs('请填写视频链接！');
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
                $activity_image->type = ActivityImage::TYPE_IMAGE_IS_PIC;
                $activity_image->save();
            }
            $activity_image = new ActivityImage();
            $activity_image->activity_id = $activity->activity_id;
            $activity_image->image_url = $video_w;
            $activity_image->type = ActivityImage::TYPE_IMAGE_IS_GIF;
            $activity_image->is_completion = ActivityImage::COMPLETE_PATH;
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

    public function editSubmit(Request $request)
    {
        $activity_id = $request->input('activity_id');
        if (!$activity_id) {
            return $this->returnAddJs('非法操作！');
        }

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

        $video_w = $request->input('video');
        if ($video_w) {
            if ($request->input('video_id')) {
                $activity_image = new ActivityImage();
                $activity_image->where('id', $request->input('video_id'))->update(
                    [
                        'image_url' => $video_w
                    ]
                );
            } else {
                $activity_image = new ActivityImage();
                $activity_image->activity_id = $activity_id;
                $activity_image->image_url = $video_w;
                $activity_image->type = ActivityImage::TYPE_IMAGE_IS_GIF;
                $activity_image->save();
            }
        }

        $image1 = $request->file('image1');
        if (!empty($image1)) {
            if (strpos($image1->getMimeType(), 'image/') === false) {
                return $this->returnAddJs('第一张图片格式不正确！');
            }

            $image1_w = $this->updateFile($image1);
            if (!$image1_w) {
                return $this->returnAddJs('第一张图片上传失败，请重新上传！');
            } else {
                if ($request->input('image1_id')) {
                    $activity_image = new ActivityImage();
                    $activity_image->where('id', $request->input('image1_id'))->update(
                        [
                            'image_url' => $image1_w
                        ]
                    );
                } else {
                    $activity_image = new ActivityImage();
                    $activity_image->activity_id = $activity_id;
                    $activity_image->image_url = $image1_w;
                    $activity_image->type = ActivityImage::TYPE_IMAGE_IS_PIC;
                    $activity_image->save();
                }
            }
        }

        $image2 = $request->file('image2');
        if (!empty($image2)) {
            if (strpos($image2->getMimeType(), 'image/') === false) {
                return $this->returnAddJs('第二张图片格式不正确！');
            }

            $image2_w = $this->updateFile($image2);
            if (!$image2_w) {
                return $this->returnAddJs('第二张图片上传失败，请重新上传！');
            } else {
                if ($request->input('image2_id')) {
                    $activity_image = new ActivityImage();
                    $activity_image->where('id', $request->input('image2_id'))->update(
                        [
                            'image_url' => $image2_w
                        ]
                    );
                } else {
                    $activity_image = new ActivityImage();
                    $activity_image->activity_id = $activity_id;
                    $activity_image->image_url = $image2_w;
                    $activity_image->type = ActivityImage::TYPE_IMAGE_IS_PIC;
                    $activity_image->save();
                }
            }
        }

        $image3 = $request->file('image3');
        if (!empty($image3)) {
            if (strpos($image3->getMimeType(), 'image/') === false) {
                return $this->returnAddJs('第三张图片格式不正确');
            }

            $image3_w = $this->updateFile($image3);
            if (!$image3_w) {
                return $this->returnAddJs('第三张图片上传失败，请重新上传');
            } else {
                if ($request->input('image3_id')) {
                    $activity_image = new ActivityImage();
                    $activity_image->where('id', $request->input('image3_id'))->update(
                        [
                            'image_url' => $image3_w
                        ]
                    );
                } else {
                    $activity_image = new ActivityImage();
                    $activity_image->activity_id = $activity_id;
                    $activity_image->image_url = $image3_w;
                    $activity_image->type = ActivityImage::TYPE_IMAGE_IS_PIC;
                    $activity_image->save();
                }
            }
        }

        $activity = new Activity();
        $bool = $activity->where('activity_id', $activity_id)->update(
            [
                'title' => $title,
                'desc' => $description,
                'statement' => $statement,
                'url' => $url,
            ]
        );
        if ($bool) {
            //插入捐赠额度
            $activity_fundraising = new ActivityFundraising();
            $activity_fundraising->where('activity_id', $activity_id)->update(
                [
                    'total_amount_price' => $price
                ]
            );
            return $this->returnAddJs('活动修改成功！');
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