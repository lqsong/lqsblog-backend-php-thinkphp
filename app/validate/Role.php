<?php
// +----------------------------------------------------------------------
// | LqsBlog - 角色验证类
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Role extends Validate
{
    protected $rule = [
        'id'  =>  'require',
        'name'  =>  'require|length:1,8',
    ];

    protected $message  =   [
        'id.require' => 'ID不能为空',
        'name.require' => '名称不能为空',
        'name.length' => '名称长度1-8个字',
    ];


    // 场景
    protected $scene = [
        'admin_service_save'  =>  ['name'],
        'admin_service_update'  =>  ['id','name'],
    ];  

}
