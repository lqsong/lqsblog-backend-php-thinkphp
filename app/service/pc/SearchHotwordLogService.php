<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 搜索热词日志 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\SearchHotwordLogModel;

class SearchHotwordLogService 
{

    /**
     * 获取配置
     *
     * @return array
     */
    public static function saveHotWordIp(string $hotword, string $ip): array
    {
        if(empty($hotword) || empty($ip)) {
            return [];
        }

        $data = [
            'hotword'=> $hotword,
            'ip'=> $ip,
            'create_time'=> date("Y-m-d H:i:s"),
        ];
        $record = SearchHotwordLogModel::create($data);

        return $record->toArray();
    }

   

}