<?php namespace App\Http\Controllers\Admin;

use App\Application\User\AuthService;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $title;
    protected $file_css;
    protected $file_js;

    public function view($page, $data = [])
    {
        $data = array_merge(
            $data,
            [
                'title' => $this->title,
                'file_css' => '/css/' . $this->file_css,
                'file_js' => '/js/' . $this->file_css,
                'user_id' => $this->getUserId(),
                'user_name' => $this->getUserName(),
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