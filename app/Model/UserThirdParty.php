<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserThirdParty extends Model
{
    const WEI_XIN = 1;
    const TX_QQ = 2;

    protected $table = 'user_third_party';
}