<?php namespace App\Http\Controllers\Admin;

use App\Application\User\AuthService;
use App\Application\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function logout()
    {
        \Cookie::queue(UserService::TOKEN_COOKIE_NAME, '', -1);
        return redirect(route('admin::login'));
    }
}