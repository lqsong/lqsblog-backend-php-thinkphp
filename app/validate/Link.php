<?php
// +----------------------------------------------------------------------
// | LqsBlog - 左邻右舍验证类
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Link extends Validate
{
    protected $rule = [
        'id'  =>  'require',
        'title'  =>  'require|length:3,50',
        'description'  =>  'max:200',
        'categoryId'  =>  'require',
    ];

    protected $message  =   [
        'id.require' => 'ID不能为空',
        'title.require' => '标题不能为空',
        'title.length' => '标题长度3-50个字',
        'description.max' => 'Description长度0-200个字',
        'categoryId.require' => '分类不能为空',
    ];


    // 场景
    protected $scene = [
        'admin_service_save'  =>  ['title','description','categoryId'],
        'admin_service_update'  =>  ['id','title','description','categoryId'],
    ];  

}
