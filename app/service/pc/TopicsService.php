<?php
// +----------------------------------------------------------------------
// | LqsBlog - PC 专题 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\pc;

use app\model\TopicsModel;
use think\facade\Db;

class TopicsService 
{

    /**
     * 获取排序字段
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getSort(int $i): string 
    {
        $sort = [ 'id', 'hit'];
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
    public static function listPage(array $paginate=['per'=>10, 'page'=>1 ], array $search= [/* 'keywords'=> '', */ 'sort'=> 0, 'order'=> 0]) : array
    {

        // 默认分页参数
        $paginate['per'] = ($paginate['per']<1 || !is_numeric($paginate['per'])) ? 10 : intval($paginate['per']);
        $paginate['page'] = ($paginate['page']<1 || !is_numeric($paginate['page'])) ? 1 : intval($paginate['page']);        
       
        // 默认 search 字段
        $search['sort'] = (empty($search['sort']) || !is_numeric($search['sort'])) ? 0 : intval($search['sort']);
        $search['order'] = (empty($search['order']) || !is_numeric($search['order'])) ? 0 : intval($search['order']);
        

        // 搜索条件
        $where = [];
        /* if(!empty($search['keywords'])) {
            $where[] = ['title', 'like', '%' . $search['keywords'] . '%'];
        } */


        $total = TopicsModel::where($where)->count();
        $topics = TopicsModel::where($where)->order(self::getSort($search['sort']),  self::getOrderType($search['order']))->page($paginate['page'], $paginate['per'])->select();        
        $response = [
            'total' => $total,
            'currentPage' => $paginate['page'],
            'list' => []
        ];
        if($topics->isEmpty()) {
            return $response;
        }

        // 设置返回数据列表
        $list = [];
        foreach($topics as $item){

            $val = [
                'id' => $item->id,
                'title' => $item->title,
                'alias' => $item->alias,
            ];            

            $content = $item->content;
            $val['quantity'] = count($content);
            if($val['quantity'] > 3) {
                for ($index = 0; $index < 3; $index++) {
                    $val['conlist'][] = $content[$index];
                }
            } else {
                $val['conlist'] = $content;
            }

            $list[] = $val;
        }

        $response['list'] = $list;
        return $response; 
        

    }


    /**
     * 根据别名查询信息，并添加浏览量
     *
     * @param string $alias 别名
     * @return array|null
     */
    public static function detailByAliasAndAddHit(string $alias) 
    {
        if(empty($alias)) {
            return null;
        }

        $info = TopicsModel::where('alias', $alias)->find();
        if(!$info) {
            return null;
        }

        $response = [
            'id' => $info->id,
            'title' => $info->title,
            'keywords' => $info->keywords,
            'description' => $info->description,
            'addtime' => $info->addtime,
            'hit' => $info->hit,
            'list' => $info->content,
        ];

        // 点击量+1
        $info->hit = Db::raw('hit+1');
        $info->save();


        return $response;
    }





}