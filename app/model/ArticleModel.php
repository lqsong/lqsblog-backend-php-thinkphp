<?php
// +----------------------------------------------------------------------
// | LqsBlog - 文章 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class ArticleModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 关闭自动写入update_time字段
    protected $updateTime = false;

    // 模型名
    protected $name = 'article';

    // 设置字段信息
    protected $schema = [
        'id'              => 'int',
        'category_id'     => 'int',
        'category_ids'    => 'string',
        'title'           => 'string',
        'keywords'        => 'string',
        'description'     => 'string',
        'thumb'           => 'string',
        'content'         => 'string',
        'tag'             => 'string',
        'interest_ids'    => 'string',
        'addtime'         => 'datetime',
        'hit'             => 'int',
        'creator_id'      => 'int',
        'create_time'     => 'datetime',
    ];



}

