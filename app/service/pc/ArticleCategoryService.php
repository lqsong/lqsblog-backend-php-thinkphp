<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 文章分类 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\ArticleCategoryModel;
use app\model\SinglePageModel;
use think\facade\Db;

class ArticleCategoryService 
{

    /**
     * 根据别名查询信息，并添加浏览量
     *
     * @param string $alias 别名
     * @return array|null
     */
    public static function selectByAliasAndAddHit(string $alias = '')
    {
        if(empty($alias)) {
            $id = 2;
            $info = SinglePageModel::find($id);
            if(!$info) {
                return null;
            }
            
            $response = [
                'name' => $info->name,
                'title' => $info->title,
                'keywords' => $info->keywords,
                'description' => $info->description,
            ];
            
            // 点击量+1
            $info->hit = Db::raw('hit+1');
            $info->save();

            return  $response;
        }



        $cateInfo = ArticleCategoryModel::where('alias', $alias)->find();
        if(!$cateInfo) {
            return null;
        }

        $response = $cateInfo->toArray();

        // 点击量+1
        $cateInfo->hit = Db::raw('hit+1');
        $cateInfo->save();


        return $response;
    }

   

}