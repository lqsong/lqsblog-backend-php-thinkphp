<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 搜索 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\ArticleCategoryModel;
use app\model\SearchModel;

class SearchService 
{

   
    /**
     * 获取排序字段
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getSort(int $i): string 
    {
        $sort = [ 'addtime', 'sid' ];
        $len = count($sort);
        return ($i > $len || $i < 0) ? $sort[0] : $sort[$i];
    }

    /**
     * 获取排序类型
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getOrderType(int $i): string
    {
        return $i === 0 ? 'desc' : 'asc';
    }


    /**
     * 获取分页信息
     * 
     * @param array $paginate=[ 'per'=> 分页数, 'page'=>页码 ] 
     * @param array $search= [ 'keywords'=> '', 'sort'=> 排序字段下标['addtime', 'sid'], 'order'=> 下标[desc 降序，asc 升序]] 搜索字段
     * @return array
     */
    public static function listPage(array $paginate=['per'=>10, 'page'=>1 ], array $search= [
        'keywords'=> '', 'noSid'=>[], 'categoryId'=>0, 'tag'=>'', 'sort'=> 0, 'order'=> 0
    ]) : array
    {

        // 默认分页参数
        $paginate['per'] = ($paginate['per']<1 || !is_numeric($paginate['per'])) ? 10 : intval($paginate['per']);
        $paginate['page'] = ($paginate['page']<1 || !is_numeric($paginate['page'])) ? 1 : intval($paginate['page']);        
       
        // 默认 search 字段
        $search['sort'] = (empty($search['sort']) || !is_numeric($search['sort'])) ? 0 : intval($search['sort']);
        $search['order'] = (empty($search['order']) || !is_numeric($search['order'])) ? 0 : intval($search['order']);
        

        // 搜索条件
        $where = [];
        if(!empty($search['keywords'])) {
            $where[] = ['title', 'like', '%' . $search['keywords'] . '%'];
        }

        if(!empty($search['noSid'])) {
            $where[] = ['sid', 'not in', $search['noSid']];
        }

        $againstArr = [];
        $againstWhere = 'id > 0';
        if(!empty($search['categoryId'])) {
            $againstArr[] = '+category_' . $search['categoryId'];
        }
        if(!empty($search['tag'])) {
            $againstArr[] = '+tag_' . $search['tag'];
        }
        if(!empty($againstArr)) {
            $againstWhere = "match(key_precise) against ('" . implode(' ', $againstArr) . "' IN BOOLEAN MODE) ";
        }

        $total = SearchModel::where($where)->whereRaw($againstWhere)->count();
        $rows = SearchModel::where($where)->whereRaw($againstWhere)->order(self::getSort($search['sort']),  self::getOrderType($search['order']))->page($paginate['page'], $paginate['per'])->select();        
        $response = [
            'total' => $total,
            'currentPage' => $paginate['page'],
            'list' => []
        ];
        if($rows->isEmpty()) {
            return $response;
        }

        // 取出分类id
        $cIds = [];
        foreach($rows as $item){
            if(!empty($item->categoryId)) {
                $cIds[] = $item->categoryId;
            }
        }
        $articleCategories = ArticleCategoryModel::where('id', 'in', $cIds)->select();
        $categorys = [];
        foreach($articleCategories as $item) {
            $categorys[$item->id] = [
                'id'=> $item->id,
                'name' => $item->name,
                'alias' => $item->alias,
            ];
        }

        // 设置返回数据列表
        $list = [];
        foreach($rows as $item){
            $list[] = [
                'id' => $item->id,
                'type' => $item->type,
                'title' => $item->title,
                'description'=> $item->description,
                'thumb' => explode('|', $item->thumb),
                'category' => (!empty($item->categoryId) && !empty($categorys[$item->categoryId])) ? $categorys[$item->categoryId] : [],
                'addtime' => $item->addtime,
            ];
        }

        $response['list'] = $list;
        return $response; 
    }


    /**
     * 获取推荐列表 function
     *
     * @param integer $limit 条数
     * @return array
     */
    public static function getRecommend(int $limit = 5): array
    {
        return SearchModel::field('sid,id,type,title,thumb')->where('thumb','<>', '')->where('thumb','not null')->order('addtime', 'desc')->limit($limit)->select()->toArray();
    }
    
    

}