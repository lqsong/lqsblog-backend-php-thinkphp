<?php
// +----------------------------------------------------------------------
// | LqsBlog - 标签验证类
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Tag extends Validate
{
    protected $rule = [
        'id'  =>  'require',
        'name'  =>  'require|length:1,10',
        'pinyin'=> 'require|length:1,10',
    ];

    protected $message  =   [
        'id.require' => 'ID不能为空',
        'name.require' => '名称不能为空',
        'name.length' => '名称长度1-10个字',
        'pinyin.require' => '拼音不能为空',
        'pinyin.length' => '拼音1-10个字符',
    ];


    // 场景
    protected $scene = [
        'admin_service_save'  =>  ['name','pinyin'],
        'admin_service_update'  =>  ['id','name','pinyin'],
    ];  

}
