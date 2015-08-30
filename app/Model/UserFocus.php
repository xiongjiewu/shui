<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserFocus extends Model
{
    const IS_ACTIVE_TRUE = 1;
    const IS_ACTIVE_FALSE = 0;

    protected $table = 'user_focus';
}
