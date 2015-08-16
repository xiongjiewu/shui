<?php namespace App\Http\Controllers;

use Input;
use App\Application\UserService;
use \Response;

class UserController extends Controller
{
    public function report()
    {
        $params = Input::All();
        $check = (new UserService())->report($params);
        if ($check['status']) {
            return Response::json(
                [
                    'code' => 0,
                    'message' => '反馈成功！',
                    'userInfo' => [],
                ]
            );
        }
        return $this->fail($check['message']);
    }
}