<?php namespace App\Http\Controllers\Admin;

use App\Application\User\UserService;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    public function login()
    {
        $this->title = '登录';
        $this->file_js = 'admin/login';
        $this->file_css = 'admin/login';
        return $this->view('admin.login');
    }

    public function loginAction(Request $request)
    {
        return response()->json(
            (new UserService())->adminLogin(
                $request->input('user_name'),
                $request->input('password')
            )
        );
    }
}