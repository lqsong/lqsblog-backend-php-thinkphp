<?php
// +----------------------------------------------------------------------
// | LqsBlog - 系统用户 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class UserModel extends Model
{
    // 模型名
    protected $name = 'sys_user';

    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'username'    => 'string',
        'password'    => 'string',
        'salt'        => 'string',
        'nickname'    => 'string',
        'locked'      => 'int',
    ];



}

