<?php
// +----------------------------------------------------------------------
// | LqsBlog - 系统资源权限关联 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class ResourcePermissionModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 模型名
    protected $name = 'sys_resource_permission';

    // 设置字段信息
    protected $schema = [
        'id'             => 'int',
        'resource_id'    => 'int',
        'permission_id'  => 'int',
    ];

    // 一对一关联权限表
    public function permission()
    {
        return $this->hasOne(PermissionModel::class,'id','permission_id');
    }



}

