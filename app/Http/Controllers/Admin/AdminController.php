<?php namespace App\Http\Controllers\Admin;

class AdminController extends BaseController
{
    public function home()
    {
        $this->title = '首页';
        return $this->view('admin.home', ['choose_id' => 0]);
    }
}