<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 站点配置 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\ConfigModel;

class ConfigService 
{

    /**
     * 获取配置
     *
     * @return array
     */
    public static function getAll(): array
    {
        $list = ConfigModel::select();
        $response =[];
        foreach ($list as $item) {
            $response[$item->name] = $item->content;
        }
        return $response;
    }

   

}