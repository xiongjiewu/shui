<?php namespace App\Http\Controllers;

use App\Http\Controllers\Admin\BaseController as AdminBaseController;

class ShareController extends AdminBaseController
{
    public function shareCode($code)
    {
        if (!$code) {
            return response(['status' => 'error']);
        }

        $this->is_mobile = true;
        $this->show_title = false;
        $this->file_css = 'Share/code';
        $this->file_js = 'Share/code';
        return $this->view('activity.share', ['code' => $code]);
    }
}