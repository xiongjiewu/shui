<?php namespace App\Http\Controllers\Admin;

use App\Application\User\UserService;
use App\Model\UserBase;
use App\Model\UserCompanyExtend;
use App\Model\UserFinancial;
use App\Model\UserImage;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function index(Request $request)
    {
        $type = $request->get('type', 1);
        $this->title = '用户管理';
        $this->file_js = 'Admin/user';
        return $this->view('admin.user',
            ['choose_id' => ($type == 1) ? 1 : 4, 'type' => $type, 'show' => 'index', 'users' => (
            (new UserService())->getList(
                $request->input('page', 1),
                $request->input('per_page', 1000000),
                $type
            )
            )]);
    }

    public function update($user_id, $type, Request $request)
    {
        $user = UserBase::find($user_id);
        if (!$user) {
            return $this->returnAddJs('用户不存在');
        }
        $user_name = $request->get('user_name');
        if (!$user_name) {
            return $this->returnAddJs('用户名不能为空');
        }

        if ($type == 2) {//商家编辑
            $water_count = $request->get('water_count');
            if (!$water_count) {
                return $this->returnAddJs('请输入亲水值！');
            }

            $send_water = $request->get('send_water');
            if (!$send_water) {
                return $this->returnAddJs('请输入护水值！');
            }

            $image_url_real1 = $request->file('image_url_real1');
            if (!$image_url_real1->isValid()) {
                return $this->returnAddJs('营业执照无效');
            }

            if (strpos($image_url_real1->getMimeType(), 'image/') === false) {
                return $this->returnAddJs('营业执照格式不正确');
            }
            $image_url_real1_w = $this->updateFile($image_url_real1);
            if (!$image_url_real1_w) {
                return $this->returnAddJs('营业执照上传失败，请重新上传！');
            }

            $image_url_real2 = $request->file('image_url_real2');
            if ($image_url_real2) {
                if (!$image_url_real2->isValid()) {
                    return $this->returnAddJs('店铺实景1无效');
                }

                if (strpos($image_url_real2->getMimeType(), 'image/') === false) {
                    return $this->returnAddJs('店铺实景1格式不正确');
                }
                $image_url_real2_w = $this->updateFile($image_url_real2);
                if (!$image_url_real2) {
                    return $this->returnAddJs('店铺实景1上传失败，请重新上传！');
                }
            }

            $image_url_real3 = $request->file('image_url_real3');
            if ($image_url_real3) {
                if (!$image_url_real3->isValid()) {
                    return $this->returnAddJs('店铺实景2无效');
                }

                if (strpos($image_url_real3->getMimeType(), 'image/') === false) {
                    return $this->returnAddJs('店铺实景2格式不正确');
                }
                $image_url_real3_w = $this->updateFile($image_url_real3);
                if (!$image_url_real3_w) {
                    return $this->returnAddJs('店铺实景2上传失败，请重新上传！');
                }
            }
        }
        $image = $request->file('image');
        if (!$image->isValid()) {
            return $this->returnAddJs('图片无效');
        }

        if (strpos($image->getMimeType(), 'image/') === false) {
            return $this->returnAddJs('图片格式不正确');
        }
        $image_w = $this->updateFile($image);
        if (!$image_w) {
            return $this->returnAddJs('图片上传失败，请重新上传！');
        }
        $user->user_name = $user_name;
        $user->save();
        $user_image = UserImage::where('user_id', $user_id)->where('type', 1)->first();
        if (!$user_image) {
            $user_image_model = new UserImage();
            $user_image_model->user_id = $user_id;
            $user_image_model->image_url = $image_w;
            $user_image_model->type = 1;
            $user_image_model->is_completion = 0;
            $user_image_model->save();
        } else {
            $user_image->image_url = $image_w;
            $user_image->save();
        }
        if ($type == 2) {
            $user_desc = $request->get('user_desc', '');
            $user_f = UserFinancial::where('user_id', $user_id)->first();
            if (!$user_f) {
                $user_f = new UserFinancial();
                $user_f->user_id = $user_id;
            }
            $user_f->water_count = $water_count;
            $user_f->send_water = $send_water;
            $user_f->save();

            $user_c = UserCompanyExtend::where('user_id', $user_id)->first();
            if (!$user_c) {
                $user_c = new UserCompanyExtend();
                $user_c->user_id = $user_id;
            }
            $user_c->user_desc = $user_desc;
            $user_c->save();

            $user_image = UserImage::where('user_id', $user_id)->where('type', 2)->first();
            if (!$user_image) {
                $user_image_model = new UserImage();
                $user_image_model->user_id = $user_id;
                $user_image_model->image_url = $image_url_real1_w;
                $user_image_model->type = 2;
                $user_image_model->is_completion = 0;
                $user_image_model->save();
            } else {
                $user_image->image_url = $image_url_real1_w;
                $user_image->save();
            }

            if ($image_url_real2_w) {
                $user_image = UserImage::where('user_id', $user_id)->where('type', 3)->first();
                if (!$user_image) {
                    $user_image_model = new UserImage();
                    $user_image_model->user_id = $user_id;
                    $user_image_model->image_url = $image_url_real2_w;
                    $user_image_model->type = 3;
                    $user_image_model->is_completion = 0;
                    $user_image_model->save();
                } else {
                    $user_image->image_url = $image_url_real2_w;
                    $user_image->save();
                }
            }
            if ($image_url_real3_w) {
                $user_image = UserImage::where('user_id', $user_id)->where('type', 3)->first();
                if (!$user_image) {
                    $user_image_model = new UserImage();
                    $user_image_model->user_id = $user_id;
                    $user_image_model->image_url = $image_url_real3_w;
                    $user_image_model->type = 3;
                    $user_image_model->is_completion = 0;
                    $user_image_model->save();
                } else {
                    $user_image->image_url = $image_url_real3_w;
                    $user_image->save();
                }
            }
        }
        return $this->returnAddJs('编辑成功！');
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

    public function show($user_id, $type, Request $request)
    {
        $this->title = '用户编辑';
        $this->file_js = 'Admin/user';
        $users = (new UserService())->show($type, $user_id);
        return $this->view('admin.user', ['choose_id' => ($type == 1) ? 1 : 4, 'type' => $type, 'show' => 'edit', 'user' => array_shift($users)]);
    }


    public function statusChange(Request $request)
    {
        $type = $request->input('type', null);
        $user_id = $request->input('user_id', null);
        if ($type === null || !is_numeric($user_id)) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => '参数错误',
                ]
            );
        }

        if ((new UserService())->updateStatus($user_id, $type)) {
            return response()->json(
                [
                    'status' => true,
                    'msg' => '操作成功',
                ]
            );
        }
        return response()->json(
            [
                'status' => false,
                'msg' => '操作失败',
            ]
        );
    }
}