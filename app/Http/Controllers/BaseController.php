<?php namespace App\Http\Controllers;

class BaseController extends Controller
{
    public function __construct()
    {
        if (Input::has('userID')) {
            return $this->fail('用户ID不存在!');
        }
    }
}