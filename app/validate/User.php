<?php
// +----------------------------------------------------------------------
// | LqsBlog - 后台管理员用户验证类
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username'  =>  'require|length:3,30',
        'password' =>  'require|length:6,15',
        'imgCode' =>  'require',
        'imgCodeToken' =>  'require',
    ];

    protected $message  =   [
        'username.require' => '用户名必须填写',
        'username.length' => '用户名3-30个字符',
        'password.require' => '密码必须填写',
        'password.length' => '密码6-15个字符',
        'imgCode.require' => '图片验证码必须填写',
        'imgCodeToken.require' => '图片验证码token必须',
    ];


    // 场景
    protected $scene = [
        'admin_service_login_user'  =>  ['username','password'],
        'admin_service_save'  =>  ['username','password'],
        'admin_service_update'  =>  ['username','password'],
        'admin_service_update_nopwd'  =>  ['username'],
    ];  

}
