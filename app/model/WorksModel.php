<?php
// +----------------------------------------------------------------------
// | LqsBlog - 作品 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class WorksModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 关闭自动写入update_time字段
    protected $updateTime = false;

    // 模型名
    protected $name = 'works';

    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'title'     => 'string',
        'keywords'     => 'string',
        'description'     => 'string',
        'thumb'     => 'string',
        'content'     => 'string',
        'tag'     => 'string',
        'addtime'     => 'datetime',
        'hit'     => 'int',
        'creator_id'     => 'int',
        'create_time'     => 'datetime',
    ];

    



}

