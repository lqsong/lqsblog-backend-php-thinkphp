<?php
// +----------------------------------------------------------------------
// | LqsBlog - 搜索热词日志 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class SearchHotwordLogModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 模型名
    protected $name = 'search_hotword_log';

    // 设置字段信息
    protected $schema = [
        'id'              => 'int',
        'ip'              => 'string',
        'create_time'     => 'datetime',
        'hotword'         => 'string',
        'country'         => 'string',
        'region'          => 'string',
        'city'            => 'string',
        'area'            => 'string',
        'isp'             => 'string',
    ];





}

