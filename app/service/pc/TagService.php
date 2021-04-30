<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 标签 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\ConfigModel;
use app\model\TagModel;
use think\facade\Db;

class TagService 
{

    /**
     * 根据名称查询信息，并添加浏览量
     *
     * @param string $name 名称
     * @return array|null
     */
    public static function getByNameAndAddHit(string $name)
    {
        if(empty($name)) {
            return null;
        }

        $info = TagModel::where('name', $name)->find();
        if(!$info) {
            return null;
        }


        $response =  $info->toArray();

        // 点击量+1
        $info->hit = Db::raw('hit+1');
        $info->save();

        return $response;
    }

   

}