<?php namespace App\Http\Controllers\Admin;

class AdminController extends BaseController
{
    public function home()
    {
        return view('admin.home');
    }
}