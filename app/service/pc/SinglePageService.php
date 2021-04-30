<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 单页面 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\SinglePageModel;

class SinglePageService 
{

    /**
     * 根据id查找详情 ，并添加浏览量
     *
     * @param integer $id id
     * @return array|null
     */
    public static function getByIdAndAddHit(int $id)
    {
        if(!is_numeric($id) || $id < 1) {
            return null;
        }

        $info = SinglePageModel::where('id',$id)->find();
        if (!$info) {
            return null;
        }

        return $info->toArray();
    }

   

}