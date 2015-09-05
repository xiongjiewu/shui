<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class InitAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_model = new \App\Model\UserBase();
        $password = 'water2015';
        $user_name = 'water2015';
        $phone = '12345678901';
        $real_password = (new \App\Application\User\UserService())->encryptPassword($password);
        if (($user = $user_model::where('password', $real_password)//账户名或者手机都可以登录
            ->where('user_name', $user_name)->isOpen()->admin()->first()) ||
            (
            $user = $user_model::where('password', $real_password)
                ->where('user_cellphone', $phone)->isOpen()->admin()->first()
            )
        ) {
            echo 'Admin has existed,user_name:' . $user_name . ',password:' . $password . ',cellphone:' . $phone, PHP_EOL;
        } else {
            $user_model->user_cellphone = $phone;
            $user_model->password = $real_password;
            $user_model->user_name = $user_name;
            $user_model->type = $user_model::TYPE_ADMIN;
            if ($user_model->save()) {
                echo 'Admin has created success,user_name:' . $user_name . ',password:' . $password . ',cellphone:' . $phone, PHP_EOL;
            } else {
                echo 'Admin has created fail,please try again.', PHP_EOL;
            }
        }
    }
}