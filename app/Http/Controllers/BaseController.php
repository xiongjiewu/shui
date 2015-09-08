<?php namespace App\Http\Controllers;

use App\Application\User\TokenService;
use \Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $user_id = '';

    public function __construct(Request $request)
    {
        if (!$request->header('App-token')) {
            exit();
        }
        $this->user_id = TokenService::tokenDecrypt($request->header('App-token'));
    }
}