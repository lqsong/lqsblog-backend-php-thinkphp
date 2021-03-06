<?php
// +----------------------------------------------------------------------
// | LqsBlog - 专题验证类
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Topics extends Validate
{
    protected $rule = [
        'id'  =>  'require',
        'title'  =>  'require|length:3,50',
        'alias'=> 'require|length:1,10',
        'keywords'  =>  'max:50',
        'description'  =>  'max:200',
        'addtime'  =>  'require',
    ];

    protected $message  =   [
        'id.require' => 'ID不能为空',
        'title.require' => '标题不能为空',
        'title.length' => '标题长度3-50个字',
        'alias.require' => '别名不能为空',
        'alias.length' => '别名长度1-10个字',
        'keywords.max' => '关键字长度0-50个字',
        'description.max' => 'Description长度0-200个字',
        'addtime.require' => '添加时间不能为空',
    ];


    // 场景
    protected $scene = [
        'admin_service_save'  =>  ['title','alias','keywords','description','addtime'],
        'admin_service_update'  =>  ['id','title','alias','keywords','description','addtime'],
    ];  

}
