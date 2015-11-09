<?php namespace App\Http\Controllers\Admin;

use App\Application\User\UserService;
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

    public function update($user_id, Request $request)
    {
        return response((new UserService())->update($user_id, $request));
    }

    public function show($type, Request $request)
    {
        $this->title = '用户编辑';
        $this->file_js = 'Admin/user';
        $users = (new UserService())->show($type, $request);
        return $this->view('admin.user', ['choose_id' => 1, 'show' => 'edit', 'user' => array_shift($users)]);
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