<?php namespace App\Http\Controllers\Admin;

use App\Application\User\AuthService;

class AdminController extends BaseController
{
    public function home()
    {
        return view('admin.home');
    }
}