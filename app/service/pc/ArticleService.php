<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 文章 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\ArticleCategoryModel;
use app\model\ArticleModel;
use think\facade\Db;

class ArticleService 
{

    /**
     * 获取排序字段
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getSort(int $i): string 
    {
        $sort = [  'addtime', 'id', 'hit' ];
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
     * 获取文章分页信息
     * 
     * @param array $paginate=[ 'per'=> 分页数, 'page'=>页码 ] 
     * @param array $search= [  'categoryId'=> '', 'sort'=> 排序字段下标[id,hit,addtime], 'order'=> 下标[desc 降序，asc 升序]] 搜索字段
     * @return array
     */
    public static function listPage(array $paginate=['per'=>10, 'page'=>1 ], array $search= ['categoryId'=> '', 'sort'=> 0, 'order'=> 0]) : array
    {

        // 默认分页参数
        $paginate['per'] = ($paginate['per']<1 || !is_numeric($paginate['per'])) ? 10 : intval($paginate['per']);
        $paginate['page'] = ($paginate['page']<1 || !is_numeric($paginate['page'])) ? 1 : intval($paginate['page']);        
       
        // 默认 search 字段
        $search['sort'] = (empty($search['sort']) || !is_numeric($search['sort'])) ? 0 : intval($search['sort']);
        $search['order'] = (empty($search['order']) || !is_numeric($search['order'])) ? 0 : intval($search['order']);
        

        // 搜索条件
        $where = [];
       

        if(!empty($search['categoryId']) && is_numeric($search['categoryId'])) {
            $where[] = ['category_id', '=', $search['categoryId']];
        }

       
        $total = ArticleModel::where($where)->count();
        $article = ArticleModel::where($where)->order(self::getSort($search['sort']),  self::getOrderType($search['order']))->page($paginate['page'], $paginate['per'])->select();        
        $response = [
            'total' => $total,
            'currentPage' => $paginate['page'],
            'list' => []
        ];
        if($article->isEmpty()) {
            return $response;
        }

        // 取出分类id
        $cIds = [];
        foreach($article as $item){
            $cIds[] = $item->categoryId;
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
        foreach($article as $item){
            $list[] = [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'thumb' => explode('|', $item->thumb),
                'category' => !empty($categorys[$item->categoryId]) ? $categorys[$item->categoryId] : [],
                'addtime' => $item->addtime,
                'tag' => $item->tag,
                'hit' => $item->hit,
            ];
        }

        $response['list'] = $list;
        return $response; 

    }


    /**
     * 根据id查找详情 ，并添加浏览量
     *
     * @param integer $id id
     * @return array|null
     */
    public static function detailByIdAndAddHit(int $id)
    {

        if(!is_numeric($id) || $id < 1) {
           return null;
        }

        $info = ArticleModel::find($id);
        if(!$info) {
            return null;
        }

        $cateInfo = ArticleCategoryModel::find($info->categoryId);
       
        $response = [
            'id' => $info->id,
            'title' => $info->title,
            'keywords' => $info->keywords,
            'description' => $info->description,
            'addtime' => $info->addtime,
            'hit' => $info->hit,
            'tag' => $info->tag,
            'interestIds' => $info->interestIds,
            'content' => $info->content,
            'category' => !$cateInfo ? [] : [
                'id' => $cateInfo->id,
                'name' => $cateInfo->name,
                'alias' => $cateInfo->alias,
            ],
        ];

        // 点击量+1
        $info->hit = Db::raw('hit+1');
        $info->save();


        return $response;
    }

    /**
     * 根据ids查找列表
     *
     * @param string $ids id ,链接字符串
     * @return array
     */
    public static function listByIds(string $ids): array 
    {
        if(empty($ids)) {
            return [];
        }

        $article = ArticleModel::where('id', 'in', $ids)->select();        
        if($article->isEmpty()) {
            return [];
        }


        // 取出分类id
        $cIds = [];
        foreach($article as $item){
            $cIds[] = $item->categoryId;
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
        foreach($article as $item){
            $list[] = [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'thumb' => explode('|', $item->thumb),
                'category' => !empty($categorys[$item->categoryId]) ? $categorys[$item->categoryId] : [],
                'addtime' => $item->addtime,
                'tag' => $item->tag,
                'hit' => $item->hit,
            ];
        }

        return $list; 

    }






}