<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 文章 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use think\facade\Db;
use app\model\ArticleCategoryModel;
use app\model\ArticleModel;
use app\model\SearchModel;
use app\validate\Article;
use enum\ResultCode;

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
        $sort = [ 'id', 'hit', 'addtime' ];
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
     * @param array $search= [ 'keywords'=> '', 'categoryid'=> '', 'addtimestart'=> '', 'addtimeend'=>'', 'tags'=> '', 'sort'=> 排序字段下标[id,hit,addtime], 'order'=> 下标[desc 降序，asc 升序]] 搜索字段
     * @return array
     */
    public static function listPage(array $paginate=['per'=>10, 'page'=>1 ], array $search= [
        'keywords'=> '', 'categoryid'=> '', 'addtimestart'=> '', 'addtimeend'=>'', 'tags'=> '', 'sort'=> 0, 'order'=> 0
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

        if(!empty($search['categoryid']) && is_numeric($search['categoryid'])) {
            $where[] = ['category_id', '=', $search['categoryid']];
        }

        if(!empty($search['addtimestart']) && !empty($search['addtimeend'])) {
            $where[] = ['addtime', 'between', [$search['addtimestart'], $search['addtimeend']]];
        }

        if(!empty($search['tags'])) {
            $where[] = ['tag', 'like', '%' . $search['tags'] . '%'];
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
                'category' => !empty($categorys[$item->categoryId]) ? $categorys[$item->categoryId] : [],
                'addtime' => $item->addtime,
                'tag' => $item->tag,
                'categoryIds' => $item->categoryIds,
            ];
        }

        $response['list'] = $list;
        return $response; 

    }


    /**
     * 插入一条记录
     *
     * @param array $info 对应文章字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info=[
        'title'=> '', 'keywords'=> '', 'description'=> '', 'categoryId'=> '', 'categoryIds'=> '', 
        'thumb'=> '', 'content'=> '', 'tag'=>'', 'interestIds'=> '', 'addtime'=> '', 'hit'=> '', 'creatorId'=> ''
    ]): array
    {
        // 验证参数，不通过会抛出异常
        validate(Article::class)->scene('admin_service_save')->check($info);

        // 创建事务
        Db::startTrans();
        $article = [];
        try {
            $data = [
                'title'=> $info['title'],
                'keywords'=> $info['keywords'],
                'description'=> $info['description'],
                'category_id'=> $info['categoryId'],
                'category_ids'=> $info['categoryIds'],
                'addtime'=> $info['addtime'],
                'create_time'=> date("Y-m-d H:i:s"),
            ];
            if(!empty($info['thumb'])) {
                $data['thumb'] = $info['thumb'];
            }
            if(!empty($info['content'])) {
                $data['content'] = $info['content'];
            }
            if(!empty($info['interestIds'])) {
                $data['interest_ids'] = $info['interestIds'];
            }
            if(!empty($info['creatorId'])) {
                $data['creator_id'] = $info['creatorId'];
            }
            if(!empty($info['tag'])) {
                $data['tag'] = $info['tag'];
            }
            if(!empty($info['hit'])) {
                $data['hit'] = $info['hit'];
            }
            $record = ArticleModel::create($data);
            $article = $record->toArray();
            
            SearchModel::create(SearchService::articleToSearch($article));
            
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            
            // 回滚事务
            Db::rollback();
            throw new \think\exception\ValidateException($e->getMessage());
        }
        
        return $article;
    }


    /**
     * 根据 ID 修改
     *
     * @param array $info 对应文章字段
     * @return boolean
     * @throws ValidateException
     */
    public static function updateById(array $info=[
        'id'=> 0, 'title'=> '', 'keywords'=> '', 'description'=> '', 'categoryId'=> '', 'categoryIds'=> '', 
        'thumb'=> '', 'content'=> '', 'tag'=>'', 'interestIds'=> '', 'addtime'=> '', 'hit'=> '', 
    ]):bool
    {
        // 验证参数，不通过会抛出异常
        validate(Article::class)->scene('admin_service_update')->check($info);

        // 创建事务
        Db::startTrans();
        try {

            $data = [
                'title'=> $info['title'],
                'keywords'=> $info['keywords'],
                'description'=> $info['description'],
                'category_id'=> $info['categoryId'],
                'category_ids'=> $info['categoryIds'],
                'addtime'=> $info['addtime'],
            ];
            if(!empty($info['thumb'])) {
                $data['thumb'] = $info['thumb'];
            }
            if(!empty($info['content'])) {
                $data['content'] = $info['content'];
            }
            if(!empty($info['interestIds'])) {
                $data['interest_ids'] = $info['interestIds'];
            }
            if(!empty($info['tag'])) {
                $data['tag'] = $info['tag'];
            }
            if(!empty($info['hit'])) {
                $data['hit'] = $info['hit'];
            }

            $record = ArticleModel::find($info['id']);
            $record->save($data);

            $searchParam = SearchService::articleToSearch($info);
            SearchModel::destroy(function($query) use($searchParam) {
                $query->where('id', $searchParam['id'])->where('type', $searchParam['type']);
            });
            SearchModel::create($searchParam);

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {

            // 回滚事务
            Db::rollback();
            throw new \think\exception\ValidateException($e->getMessage());
        }


        return true;
    }


    /**
     * 根据 ID 删除
     *
     * @param integer $id 文章id
     * @return boolean
     * @throws ValidateException
     */
    public static function removeById(int $id): bool
    {
        if(!is_numeric($id) || $id < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        // 创建事务
        Db::startTrans();
        $response = false;
        try {

            $response = ArticleModel::destroy(function($query) use($id) {
                $query->where('id', $id);
            });

            $searchParam = SearchService::articleToSearch(['id'=>$id]);
            SearchModel::destroy(function($query) use($searchParam) {
                $query->where('id', $searchParam['id'])->where('type', $searchParam['type']);
            });

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {

            // 回滚事务
            Db::rollback();
            throw new \think\exception\ValidateException($e->getMessage());
        }

        return $response;
    }


    /**
     * 根据 ID 获取 ArticleInterest 详情
     *
     * @param integer $id 文章id
     * @return array
     * @throws ValidateException
     */
    public static function getArticleInterestById(int $id): array
    {

        if(!is_numeric($id) || $id < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        $article = ArticleModel::find($id);
        if(!$article) {
            throw new \think\exception\ValidateException(ResultCode::NOT_FOUND['msg']);
        }

        $interestIds = explode(',', $article->interest_ids);
        $interestArticle = ArticleModel::where('id', 'in', $interestIds)->field('id,title,addtime')->select()->toArray();

        return [
            'id'=> $article->id,
            'categoryId'=> $article->categoryId,
            'categoryIds'=> $article->categoryIds,
            'title'=> $article->title,
            'keywords'=> $article->keywords,
            'description'=> $article->description,
            'thumb'=> $article->thumb,
            'content'=> $article->content,
            'tag'=> $article->tag,
            'interestIds'=> $article->interestIds,
            'addtime'=> $article->addtime,
            'hit'=> $article->hit,
            'creatorId'=> $article->creatorId,
            'createTime'=> $article->createTime,
            'interest'=> $interestArticle,
        ];
    }


    /**
     * 统计 - 日新增，总量，日同比，周同比
     *
     * @return array
     */
    public static function getArticleDailyNew(): array
    {
        // 今天星期几,w为星期几的数字形式,这里0为周日
        $weekNum = date('w');
        $weekNum = $weekNum===0?7:$weekNum;
        // 当前日期
        $now = date("Y-m-d");
        // 前一天日期
        $dayBefore = date('Y-m-d',strtotime('-1 day'));
        // 后一天日期
        $dayAfter = date("Y-m-d",strtotime("+1 day"));
        // 获取本周一日期
        $mondayTime = time() - ( $weekNum- 1) * 24 * 3600;
        $monday = date('Y-m-d', $mondayTime);
        // 获取上周一日期
        $LastMonday = date('Y-m-d', strtotime('-7 day', $mondayTime));

        // 总文章数
        $total = ArticleModel::count();

        // 今天新增文章
        $num = ArticleModel::where([
            ['create_time','>=', $now],
            ['create_time', '<', $dayAfter]
        ])->count();

        // 前一天新增文章
        $numBefore = ArticleModel::where([
            ['create_time','>=', $dayBefore],
            ['create_time', '<', $now]
        ])->count();

        // 日同比%
        $dayCompare = $numBefore > 0 ? round(($num - $numBefore) / $numBefore * 10000) / 100 : $num * 100;

        // 本周新增文章数
        $week = ArticleModel::where([
            ['create_time','>=', $monday],
            ['create_time', '<', $dayAfter]
        ])->count();

        // 上周新增文章
        $weekBefore = ArticleModel::where([
            ['create_time','>=', $LastMonday],
            ['create_time', '<', $monday]
        ])->count();

        // 周同比%
        $weekCompare = $weekBefore > 0 ? round(($week - $weekBefore) / $weekBefore * 10000) / 100 : $week * 100;

        return [
            'total' => $total,
            'num' => $num,
            'day'=> $dayCompare,
            'week' => $weekCompare,
            
            
        ];
    }


}