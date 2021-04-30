<?php
// +----------------------------------------------------------------------
// | LqsBlog - 专题 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class TopicsModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 关闭自动写入update_time字段
    protected $updateTime = false;

    // 模型名
    protected $name = 'topics';

    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'title'     => 'string',
        'alias'     => 'string',
        'keywords'     => 'string',
        'description'     => 'string',
        'content'     => 'string',
        'addtime'     => 'datetime',
        'hit'     => 'int',
        'creator_id'     => 'int',
        'create_time'     => 'datetime',
    ];

    /**
     * 设置器-重置content
     *
     * @param [type] $value
     * @return void
     */
    public function setContentAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 获取器-重置content
     *
     * @param [type] $value
     * @return void
     */
    public function getContentAttr($value)
    {
        return json_decode($value);
    }

    



}

