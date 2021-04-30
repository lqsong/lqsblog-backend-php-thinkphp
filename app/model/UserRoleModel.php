<?php
// +----------------------------------------------------------------------
// | LqsBlog - 系统用户角色关系 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class UserRoleModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 模型名
    protected $name = 'sys_user_role';

    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'user_id'     => 'int',
        'role_id'     => 'int',
    ];

    // 一对多关联角色资源关联表
    public function roleResource() 
    {
        return $this->hasMany(RoleResourceModel::class,'role_id', 'role_id');
    }

    // 一对一关联角色表
    public function role() {
        return $this->hasOne(RoleModel::class, 'id', 'role_id');
    }



}

