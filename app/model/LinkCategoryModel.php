<?php
// +----------------------------------------------------------------------
// | LqsBlog - 左邻右舍分类 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class LinkCategoryModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 模型名
    protected $name = 'link_category';

    // 设置字段信息
    protected $schema = [
        'id'              => 'int',
        'name'            => 'string',
        'alias'           => 'string',
        'sort'             => 'int',
    ];





}

