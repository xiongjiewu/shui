<?php namespace App\Application\User;

use App\Model\UserBase;

class AuthService
{
    private static $user_id;
    private static $user_type;
    private static $user_name;

    public static function setUserByToken($token)
    {
        $user = TokenService::decode($token);
        self::setUserId(isset($user['user_id']) ? $user['user_id'] : null);
        self::setUserType(isset($user['type']) ? $user['type'] : null);
        self::setUserName(isset($user['user_name']) ? $user['user_name'] : null);
    }

    private static function setUserId($user_id)
    {
        self::$user_id = $user_id;
    }

    private static function setUserType($user_type)
    {
        self::$user_type = $user_type;
    }

    private static function setUserName($user_name)
    {
        self::$user_name = $user_name;
    }

    public static function getUserId()
    {
        return self::$user_id;
    }

    public static function getUserType()
    {
        return self::$user_type;
    }

    public static function getUserName()
    {
        return self::$user_name;
    }

    public static function check()
    {
        if (self::getUserId() && self::getUserType()) {
            return true;
        }
        return false;
    }

    public static function isAdmin()
    {
        return (self::$user_type == UserBase::TYPE_ADMIN);
    }
}