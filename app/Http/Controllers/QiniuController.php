<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    /**
     * 下载二维码
     * @param $string
     * @return resource
     */
    public function downloadQrcode($string)
    {
        $path = getenv('QR_CODE_URL') . $string . '.png';
        if (!file_exists($path)) {
            $this->localChatQrcode($string, $path, true);
        }
        return Response::download($path, $string . '.png');
    }

    /**
     * @param Request $request
     */
    public function alipaySure(Request $request)
    {
        /** @var $alipay */
        $alipay = app('alipay.mobile');
        $alipay->setOutTradeNo('order_id');
        $alipay->setTotalFee('order_price');
        $alipay->setSubject('goods_name');
        $alipay->setBody('goods_description');
        dd($alipay->getPayPara());
    }
}