<?php namespace App\Http\Controllers\Admin;

class ActivityController extends BaseController
{
    public function add()
    {
        $this->title = '新增公益活动';
        $this->file_js = 'Admin/activity_add';
        return $this->view('admin.activity.add', ['choose_id' => 2]);
    }

    public function addSubmit()
    {

    }
}