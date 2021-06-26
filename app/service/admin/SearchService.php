<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 搜索 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\ArticleCategoryModel;
use app\model\SearchModel;

class SearchService 
{

    /**
     * 文章字段生成对应搜索字段
     *
     * @param array $article 对应文章字段
     * @return array
     */
    public static function articleToSearch(array $article): array {

        $article['categoryId'] =  empty($article['categoryId'])?'':$article['categoryId'];
        $article['title'] =  empty($article['title'])?'':$article['title'];
        $article['keywords'] =  empty($article['keywords'])?'':$article['keywords'];
        $article['description'] =  empty($article['description'])?'':$article['description'];
        $article['thumb'] =  empty($article['thumb'])?'':$article['thumb'];
        $article['tag'] =  empty($article['tag'])?'':$article['tag'];
        $article['addtime'] =  empty($article['addtime'])?'':$article['addtime'];
        $article['categoryIds'] =  empty($article['categoryIds'])?'':$article['categoryIds'];

        return [
            'id'=> $article['id'],
            'type'=> 1,
            'category_id'=> $article['categoryId'],
            'title'=> $article['title'],
            'keywords'=> $article['keywords'],
            'description'=> $article['description'],
            'thumb'=> $article['thumb'],
            'tag'=> $article['tag'],
            'addtime'=> $article['addtime'],
            'key_precise'=> self::getKeyPrecise($article['categoryIds'], $article['tag']),
        ];
    }

    /**
     * 作品字段生成对应搜索字段
     *
     * @param array $works 对应作品字段
     * @return array
     */

    public static function worksToSearch(array $works): array {

        $works['title'] =  empty($works['title'])?'':$works['title'];
        $works['keywords'] =  empty($works['keywords'])?'':$works['keywords'];
        $works['description'] =  empty($works['description'])?'':$works['description'];
        $works['thumb'] =  empty($works['thumb'])?'':$works['thumb'];
        $works['tag'] =  empty($works['tag'])?'':$works['tag'];
        $works['addtime'] =  empty($works['addtime'])?'':$works['addtime'];

        return [
            'id'=> $works['id'],
            'type'=> 2,
            'category_id'=> '',
            'title'=> $works['title'],
            'keywords'=> $works['keywords'],
            'description'=> $works['description'],
            'thumb'=> $works['thumb'],
            'tag'=> $works['tag'],
            'addtime'=> $works['addtime'],
            'key_precise'=> self::getKeyPrecise('', $works['tag']),
        ];
    }


    /**
     * 生成 搜索关键词
     *
     * @param string $categoryIds 分类id , 链接
     * @param string $tag 标签 , 链接
     * @return string
     */
    public static function getKeyPrecise(string $categoryIds, string $tag): string
    {
        $keyArr = [];
        if(!empty($categoryIds)) {
            $catArr = explode(',', $categoryIds);
            foreach ($catArr as  $value) {
                $keyArr[] = 'category_' . $value;
            }
        }

        if(!empty($tag)) {
            $tagArr = explode(',', $tag);
            foreach ($tagArr as $value) {
                $keyArr[] = 'tag_' . $value;
            }
        }

        return implode(' ', $keyArr);
    }


    /**
     * 获取排序字段
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getSort(int $i): string 
    {
        $sort = [ 'sid', 'addtime' ];
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
     * @param array $search= [ 'keywords'=> '', 'sort'=> 排序字段下标[id,hit,addtime], 'order'=> 下标[desc 降序，asc 升序]] 搜索字段
     * @return array
     */
    public static function listPage(array $paginate=['per'=>10, 'page'=>1 ], array $search= ['keywords'=> '', 'sort'=> 0, 'order'=> 0]) : array
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


        $total = SearchModel::where($where)->count();
        $rows = SearchModel::where($where)->order(self::getSort($search['sort']),  self::getOrderType($search['order']))->page($paginate['page'], $paginate['per'])->select();        
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
                'thumb' => $item->thumb,
                'category' => (!empty($item->categoryId) && !empty($categorys[$item->categoryId])) ? $categorys[$item->categoryId] : [],
                'addtime' => $item->addtime,
            ];
        }

        $response['list'] = $list;
        return $response; 
    }
    
    

}