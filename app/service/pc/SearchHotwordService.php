<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 搜索热词 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\SearchHotwordModel;
use think\facade\Db;

class SearchHotwordService 
{

    /**
     * 插入一条关键词,存在则数量+1
     *
     * @param string $hotWord 热词
     * @return array
     */
    public static function saveHotWord(string $hotWord): array
    {
        if(empty($hotWord)) {
            return [];
        }


        $info = SearchHotwordModel::where('name', $hotWord)->find();
        if(!$info) {
            $data = [
                'name'=> $hotWord,
            ];
            $record = SearchHotwordModel::create($data);
            return $record->toArray();
        }


        $response =  $info->toArray();

        // 点击量+1
        $info->hit = Db::raw('hit+1');
        $info->save();

        return $response;

    }

   

}