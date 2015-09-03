<?php namespace App\Http\Controllers\Admin;

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
            ]
        );
        return view($page, $data);
    }
}