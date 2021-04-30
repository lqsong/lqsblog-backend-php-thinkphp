<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 标签日志 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\TagLogModel;

class TagLogService 
{

    /**
     * 插入一条标签、IP记录
     *
     * @param [type] $tag 标签名称
     * @param [type] $ip ip地址
     * @return array
     */
    public static function saveTagIp(string $tag, string $ip): array
    {

        if(empty($tag) || empty($ip)) {
            return [];
        }

        $data = [
            'tag'=> $tag,
            'ip'=> $ip,
            'create_time'=> date("Y-m-d H:i:s"),
        ];
        $record = TagLogModel::create($data);

        return $record->toArray();
    }

   

}