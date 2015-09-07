<?php namespace App\Application\User;

class TokenService
{
    //先写死，后续还成配置
    const AES_IV = '12345678123456xx';
    const AES_PRIVATE_KEY = '1234567w812a3456xx';

    /**
     * 生成token
     * @param $user_name
     * @param $user_id
     * @param $type
     * @return string
     */
    public static function create($user_name, $user_id, $type)
    {
        return self::encode([
            'user_name' => $user_name,
            'user_id' => $user_id,
            'type' => $type,
        ]);
    }

    private static function encode($data)
    {
        if (!is_numeric($data) && !is_string($data)) {
            $data = json_encode($data);
        }
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, self::AES_PRIVATE_KEY, $data, MCRYPT_MODE_CBC, self::AES_IV);
        return base64_encode($encrypted);
    }

    /**
     * @param $data
     * @return mixed|string
     */
    public static function decode($data)
    {
        $encrypted = base64_decode($data);
        $data = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, self::AES_PRIVATE_KEY, $encrypted, MCRYPT_MODE_CBC, self::AES_IV), "\0");
        $rt = json_decode($data, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            $rt = $data;
        }
        return $rt;
    }

    public static function tokenEncode($data, $key = 'water')
    {
        $data = $key . $data;
        $key = md5($key);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        }
        return base64_encode($str);
    }

    public static function tokenDecrypt($data, $key = 'water')
    {
        $y = $key;
        $key = md5($key);
        $x = 0;
        $data = base64_decode($data);
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return str_replace($y, '', $str);
    }
}
