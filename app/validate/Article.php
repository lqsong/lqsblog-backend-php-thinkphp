<?php
// +----------------------------------------------------------------------
// | LqsBlog - 文章验证类
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'id'  =>  'require',
        'title'  =>  'require|length:3,100',
        'keywords'  =>  'max:50',
        'description'  =>  'max:200',
        'categoryId'  =>  'require',
        'categoryIds'  =>  'require',
        'addtime'  =>  'require',
    ];

    protected $message  =   [
        'id.require' => 'ID不能为空',
        'title.require' => '标题不能为空',
        'title.length' => '标题长度3-100个字',
        'keywords.max' => '关键字长度0-50个字',
        'description.max' => 'Description长度0-200个字',
        'categoryId.require' => '分类不能为空',
        'categoryIds.require' => '分类不能为空',
        'addtime.require' => '添加时间不能为空',
    ];


    // 场景
    protected $scene = [
        'admin_service_save'  =>  ['title','keywords','description','categoryId','categoryIds','addtime'],
        'admin_service_update'  =>  ['id','title','keywords','description','categoryId','categoryIds','addtime'],
    ];  

}
