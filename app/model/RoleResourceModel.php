<?php
// +----------------------------------------------------------------------
// | LqsBlog - 系统角色资源关联 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class RoleResourceModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 模型名
    protected $name = 'sys_role_resource';

    // 设置字段信息
    protected $schema = [
        'id'           => 'int',
        'role_id'      => 'int',
        'resource_id'  => 'int',
    ];

    // 一对一关联资源表
    public function resource() {
        return $this->hasOne(ResourceModel::class, 'id', 'resource_id');
    }



}

