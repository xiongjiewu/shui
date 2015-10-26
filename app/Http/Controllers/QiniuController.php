<?php namespace App\Http\Controllers;

use Qiniu\Auth;
use \Response;

class QiniuController extends Controller
{
    /**
     * 获得七牛云token
     */
    public function getToken()
    {
        $accessKey = getenv('QINIU_ACCESSKEY');
        $secretKey = getenv('QINIU_SECRETKEY');
        $file = getenv('QINIU_FILE');
        $auth = new Auth($accessKey, $secretKey);
        $token = $auth->uploadToken($file);
        return Response::json([
            'code' => 0,
            'message' => '申请成功！',
            'token' => $token,
            'qiniuhost' => getenv('QINIU_HOST')
        ]);
    }
}