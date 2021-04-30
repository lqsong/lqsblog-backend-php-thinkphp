<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 左邻右舍 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\LinkCategoryModel;
use app\model\LinkModel;

class LinkService 
{

    /**
     * 获取所有分类链接列表
     *
     * @return array
     */
    public static function selectLinkCategoryAll(): array
    {
        $list = LinkModel::select();
        $listMap = [];
        foreach ($list as  $value) {
            $newItem = [
                'id' => $value->id,
                'title' => $value->title,
                'description' => $value->description,
                'href' => $value->href,
                'logo' => $value->logo,
            ];

            $listMap[$value->categoryId][] = $newItem;
        }

        $cList = LinkCategoryModel::select();
        $linkCategory = [];
        foreach ($cList as $value) {
            $linkCategory[] = [
                'name' => $value->name,
                'children' => $listMap[$value->id],
            ];
        }

        return $linkCategory;
    }


    /**
     * 根据ids查找列表
     *
     * @param string $ids id ,链接字符串
     * @return array
     */
    public static function getByCategoryIds(string $ids): array 
    {
        if(empty($ids)) {
            return [];
        }

        $links = LinkModel::field('id,title,description,logo,href')->where('id', 'in', $ids)->select();        
        return $links->toArray();
    }

   

}