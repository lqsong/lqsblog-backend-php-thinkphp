<?php
// +----------------------------------------------------------------------
// | LqsBlog - 权限api验证类
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Permission extends Validate
{
    protected $rule = [
        'id'  =>  'require',
        'pid' => 'require|number',
        'name'  =>  'require|length:1,8',
        'permission'  =>  'max:100',
    ];

    protected $message  =   [
        'id.require' => 'ID不能为空',
        'pid.require' => '父ID不能为空',
        'pid.number' => '父ID必须是数字',
        'name.require' => '名称不能为空',
        'name.length' => '名称长度1-8个字',
        'permission.max' => '权限编号最长100个字',
    ];


    // 场景
    protected $scene = [
        'admin_service_save'  =>  ['pid','name','permission'],
        'admin_service_update'  =>  ['id',/* 'pid', */'name','permission'],
    ];  

}
