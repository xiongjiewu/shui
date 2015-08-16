<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserBase extends Model
{
    protected $table = 'user_base';

    protected $primaryKey = 'user_id';
}
