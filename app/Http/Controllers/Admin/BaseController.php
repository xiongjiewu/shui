<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $title;

    public function view($page, $data = [])
    {
        $data = array_merge(
            $data,
            [
                'title' => $this->title,
            ]
        );
        return view($page, $data);
    }
}