<?php
// +----------------------------------------------------------------------
// | LqsBlog - 系统用户角色 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class RoleModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 模型名
    protected $name = 'sys_role';

    // 设置字段信息
    protected $schema = [
        'id'               => 'int',
        'name'             => 'string',
        'description'      => 'string',
        'resources'        => 'string',
        'resources_level'  => 'string',
    ];



}

