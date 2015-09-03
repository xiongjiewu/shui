<?php namespace App\Http\Controllers\Admin;

use App\Application\User\UserService;
use Illuminate\Http\Request;
use Input;

class LoginController extends BaseController
{
    public function login()
    {
        $this->title = '登录';
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