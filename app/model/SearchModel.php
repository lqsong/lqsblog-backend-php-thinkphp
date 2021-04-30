<?php
// +----------------------------------------------------------------------
// | LqsBlog - 搜索 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;
use Godruoyi\Snowflake\Sonyflake;

class SearchModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 模型名
    protected $name = 'search';

    // 设置字段信息
    protected $schema = [
        'sid'         => 'int',
        'id'          => 'int',
        'type'     => 'int',
        'category_id'     => 'int',
        'title' => 'string',
        'keywords' => 'string',
        'description' => 'string',
        'thumb' => 'string',
        'tag' => 'string',
        'addtime' => 'datetime',
        'key_precise' => 'string'
    ];

    /**
     * 新增前
     *
     * @param [type] $search
     * @return void
     */
    public static function onBeforeInsert($search)
    {
        $search->sid = (new Sonyflake())->id(); // 设置雪花算法ID
    }





}

