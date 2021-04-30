<?php
// +----------------------------------------------------------------------
// | LqsBlog - 文章分类验证类
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class ArticleCategory extends Validate
{
    protected $rule = [
        'id'  =>  'require',
        'pid' => 'require|number',
        'name'  =>  'require|length:1,8',
        'alias'  =>  'require|length:1,10',
        'title'  =>  'max:30',
        'keywords'  =>  'max:50',
        'description'  =>  'max:200',
    ];

    protected $message  =   [
        'id.require' => 'ID不能为空',
        'pid.require' => '父ID不能为空',
        'pid.number' => '父ID必须是数字',
        'name.require' => '名称不能为空',
        'name.length' => '名称长度1-8个字',
        'alias.require' => '别名不能为空',
        'alias.length' => '别名1-10个字符',
        'title.max' => 'Title最长30个字',
        'keywords.max' => 'Keywords最长50个字',
        'description.max' => 'Description长度0-200个字',
    ];


    // 场景
    protected $scene = [
        'admin_service_save'  =>  ['pid','name','alias','title','keywords','description'],
        'admin_service_update'  =>  ['id','name','alias','title','keywords','description'],
    ];  

}
