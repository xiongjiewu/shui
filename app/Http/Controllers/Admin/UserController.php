<?php namespace App\Http\Controllers\Admin;

class UserController extends BaseController
{
    public function index()
    {
        $this->title = '用户管理';
        return $this->view('admin.home', ['choose_id' => 1]);
    }
}