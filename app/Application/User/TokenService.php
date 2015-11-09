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
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, md5(self::AES_PRIVATE_KEY), $data, MCRYPT_MODE_CBC, self::AES_IV);
        return base64_encode($encrypted);
    }

    /**
     * @param $data
     * @return mixed|string
     */
    public static function decode($data)
    {
        $encrypted = base64_decode($data);
        $data = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, md5(self::AES_PRIVATE_KEY), $encrypted, MCRYPT_MODE_CBC, self::AES_IV), "\0");
        $rt = json_decode($data, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            $rt = $data;
        }
        return $rt;
    }

    public static function tokenEncode($data)
    {
        return base64_encode($data);
    }

    public static function tokenDecrypt($data)
    {
        return (int)base64_decode($data);
    }
}
