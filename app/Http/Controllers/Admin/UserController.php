<?php namespace App\Http\Controllers\Admin;

use App\Application\User\UserService;
use App\Model\UserBase;
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
        if ($type == 1) {//用户编辑
            $user_name = $request->get('user_name');
            if (!$user_name) {
                return $this->returnAddJs('用户名不能为空');
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
            $user = UserBase::find($user_id);
            if (!$user) {
                return $this->returnAddJs('用户不存在');
            }
            $user->user_name = $user_name;
            $user->save();
            $user_image = UserImage::where('user_id', $user_id)->first();
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
            return $this->returnAddJs('编辑成功！');
        } elseif ($type == 2) {//商家编辑

        }
        return response((new UserService())->update($user_id, $request));
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