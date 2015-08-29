<?php namespace App\Http\Controllers\Admin;

use App\Application\User\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;

class LoginController extends Controller
{
    public function login()
    {
        return view('admin.login');
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