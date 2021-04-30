<?php
// +----------------------------------------------------------------------
// | LqsBlog - 文件 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class AttachmentModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 关闭自动写入update_time字段
    protected $updateTime = false;

    // 模型名
    protected $name = 'attachment';

    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'file_old_name'     => 'string',
        'file_name'     => 'string',
        'file_sub_dir'     => 'string',
        'file_type'     => 'string',
        'file_size'     => 'int',
        'file_suffix'     => 'string',
        'creator_id'     => 'int',
        'create_time'     => 'datetime',
    ];

    



}

