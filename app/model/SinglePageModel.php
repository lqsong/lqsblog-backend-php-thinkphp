<?php
// +----------------------------------------------------------------------
// | LqsBlog - 单页面 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class SinglePageModel extends Model
{
    // 模型名
    protected $name = 'single_page';

    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'alias'       => 'string',
        'title'       => 'string',
        'keywords'    => 'string',
        'description' => 'string',
        'content'     => 'string',
        'hit'         => 'int',
    ];



}

