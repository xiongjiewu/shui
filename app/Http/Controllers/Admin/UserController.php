<?php namespace App\Http\Controllers\Admin;

use App\Application\User\UserService;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function index(Request $request)
    {
        $this->title = '用户管理';
        $this->file_js = 'Admin/user';
        return $this->view('admin.user', ['choose_id' => 1, 'users' => (
        (new UserService())->getList(
            $request->input('page', 1),
            $request->input('per_page', 1000000)
        )
        )]);
    }

    public function update(Request $request)
    {
        $this->title = '用户编辑';
        $this->file_js = 'Admin/user';
        return $this->view('admin.user', ['choose_id' => 1, 'users' => (
        (new UserService())->update($request)
        )]);
    }

    public function show($type, Request $request)
    {
        $this->title = '用户编辑';
        $this->file_js = 'Admin/user';
        return $this->view('admin.user', ['choose_id' => 1, 'users' => (
        (new UserService())->show($type, $request)
        )]);
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