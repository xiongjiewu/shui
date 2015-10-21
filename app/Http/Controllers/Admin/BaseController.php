<?php namespace App\Http\Controllers\Admin;

use App\Application\User\AuthService;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $title;
    protected $file_css;
    protected $file_js;
    protected $show_title = true;
    protected $is_mobile = false;

    public function view($page, $data = [])
    {
        $data = array_merge(
            $data,
            [
                'title' => $this->title,
                'file_css' => $this->file_css ? '/css/' . $this->file_css : null,
                'file_js' => $this->file_js ? '/js/' . $this->file_js : null,
                'user_id' => $this->getUserId(),
                'user_name' => $this->getUserName(),
                'show_title' => $this->show_title,
                'is_mobile' => $this->is_mobile,
            ]
        );
        return view($page, $data);
    }

    public function getUserId()
    {
        return AuthService::getUserId();
    }

    public function getUserName()
    {
        return AuthService::getUserName();
    }
}