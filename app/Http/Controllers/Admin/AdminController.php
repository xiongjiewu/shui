<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Input;

class AdminController extends Controller
{
    public function home()
    {
        return view('admin.home');
    }
}