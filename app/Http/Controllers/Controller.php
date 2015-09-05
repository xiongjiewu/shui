<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    public function fail($msg = '')
    {
        return \Response::json(
            [
                'code' => -1,
                'message' => $msg,
            ]
        );
    }

    /**
     * @param $file \Symfony\Component\HttpFoundation\File\UploadedFile|array
     * @return bool
     */
    protected function updateFile($file)
    {
        $file_name = md5(microtime(true) . $file->getFileName()) . '.' . $file->getClientOriginalExtension();
        if ($file->move(getenv('FILE_PATH'), $file_name)) {
            return $file_name;
        }
        return false;
    }
}
