<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserRelationship extends Model
{
    protected $table = 'user_relationship';

    public static function getGuestRate()
    {
        return getenv('GUEST_USER_GET_WATER');
    }
}