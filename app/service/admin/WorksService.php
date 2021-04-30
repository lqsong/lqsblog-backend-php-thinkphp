<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 作品 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use think\facade\Db;
use app\model\SearchModel;
use app\model\WorksModel;
use app\validate\Works;
use enum\ResultCode;

class WorksService 
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
     * 获取分页信息
     * 
     * @param array $paginate=[ 'per'=> 分页数, 'page'=>页码 ] 
     * @param array $search= [ 'keywords'=> '', 'addtimestart'=> '', 'addtimeend'=>'', 'tags'=> '', 'sort'=> 排序字段下标[id,hit,addtime], 'order'=> 下标[desc 降序，asc 升序]] 搜索字段
     * @return array
     */
    public static function listPage(array $paginate=['per'=>10, 'page'=>1 ], array $search= [
        'keywords'=> '', 'addtimestart'=> '', 'addtimeend'=>'', 'tags'=> '', 'sort'=> 0, 'order'=> 0
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

        if(!empty($search['addtimestart']) && !empty($search['addtimeend'])) {
            $where[] = ['addtime', 'between', [$search['addtimestart'], $search['addtimeend']]];
        }

        if(!empty($search['tags'])) {
            $where[] = ['tag', 'like', '%' . $search['tags'] . '%'];
        }


        $total = WorksModel::where($where)->count();
        $works = WorksModel::where($where)->order(self::getSort($search['sort']),  self::getOrderType($search['order']))->page($paginate['page'], $paginate['per'])->select();        
        return [
            'total' => $total,
            'currentPage' => $paginate['page'],
            'list' => $works->hidden(['content'])->toArray()
        ];

    }


    /**
     * 插入一条记录
     *
     * @param array $info 对应字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info= [
        'title'=> '', 'keywords'=> '', 'description'=> '', 'thumb'=> '', 'content'=> '', 'tag'=>'', 'addtime'=> '', 'hit'=> '', 'creatorId'=> ''
    ]): array
    {

        // 验证参数，不通过会抛出异常
        validate(Works::class)->scene('admin_service_save')->check($info);
        // 创建事务
        Db::startTrans();
        $works = [];
        try {
            $data = [
                'title'=> $info['title'],
                'keywords'=> empty($info['keywords'])?'':$info['keywords'],
                'description'=> empty($info['description'])?'':$info['description'],
                'addtime'=> $info['addtime'],
                'create_time'=> date("Y-m-d H:i:s"),
            ];
            if(!empty($info['thumb'])) {
                $data['thumb'] = $info['thumb'];
            }
            if(!empty($info['content'])) {
                $data['content'] = $info['content'];
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
            $record = WorksModel::create($data);
            $works = $record->toArray();
            
            SearchModel::create(SearchService::worksToSearch($works));
            
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            
            // 回滚事务
            Db::rollback();
            throw new \think\exception\ValidateException($e->getMessage());
        }
        
        return $works;
    }


    /**
     * 根据 ID 修改
     *
     * @param array $info 对应字段
     * @return boolean
     * @throws ValidateException
     */
    public static function updateById(array $info= [
        'id'=>0, 'title'=> '', 'keywords'=> '', 'description'=> '', 'thumb'=> '', 'content'=> '', 'tag'=>'', 'addtime'=> '', 'hit'=> ''
    ]): bool
    {
        // 验证参数，不通过会抛出异常
        validate(Works::class)->scene('admin_service_update')->check($info);

        // 创建事务
        Db::startTrans();
        try {

            $data = [
                'title'=> $info['title'],
                'keywords'=> empty($info['keywords'])?'':$info['keywords'],
                'description'=> empty($info['description'])?'':$info['description'],
                'addtime'=> $info['addtime'],
            ];
            if(!empty($info['thumb'])) {
                $data['thumb'] = $info['thumb'];
            }
            if(!empty($info['content'])) {
                $data['content'] = $info['content'];
            }
            if(!empty($info['tag'])) {
                $data['tag'] = $info['tag'];
            }
            if(!empty($info['hit'])) {
                $data['hit'] = $info['hit'];
            }

            $record = WorksModel::find($info['id']);
            $record->save($data);

            $searchParam = SearchService::worksToSearch($info);
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
     * @param integer $id id
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

            $response = WorksModel::destroy(function($query) use($id) {
                $query->where('id', $id);
            });

            $searchParam = SearchService::worksToSearch(['id'=>$id]);
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
     * 根据 ID 详情
     *
     * @param integer $id id
     * @return array
     * @throws ValidateException
     */
    public static function getById(int $id): array
    {
        if(!is_numeric($id) || $id < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        $works = WorksModel::find($id);
        if(!$works) {
            throw new \think\exception\ValidateException(ResultCode::NOT_FOUND['msg']);
        }

        return $works->toArray();
    }


    /**
     * 统计 - 周新增，总量，chart数据
     *
     * @return array
     */
    public static function getStatsTotalChart(): array
    {
        // 今天星期几,w为星期几的数字形式,这里0为周日
        $weekNum = date('w');
        $weekNum = $weekNum===0?7:$weekNum;
        // 当前日期
        $now = date("Y-m-d");
        // 后一天日期
        $dayAfter = date("Y-m-d",strtotime("+1 day"));
        // 获取本周一日期
        $mondayTime = time() - ( $weekNum- 1) * 24 * 3600;
        $monday = date('Y-m-d', $mondayTime);
        // 7天前日期
        $sevenDaysAgo = date('Y-m-d',strtotime('-7 day'));

        // 总数
        $total = WorksModel::count();

        // 本周新增数
        $week = WorksModel::where([
            ['create_time','>=', $monday],
            ['create_time', '<', $dayAfter]
        ])->count();

        $day = [
            $sevenDaysAgo, // 7天前
            date('Y-m-d',strtotime('-6 day')), // 6天前
            date('Y-m-d',strtotime('-5 day')), // 5天前
            date('Y-m-d',strtotime('-4 day')), // 4天前
            date('Y-m-d',strtotime('-3 day')), // 3天前
            date('Y-m-d',strtotime('-2 day')), // 2天前
            date('Y-m-d',strtotime('-1 day')), // 1天前
        ];

        $statsDayTotal = WorksModel::field('DATE_FORMAT(create_time,"%Y-%m-%d") as day,COUNT(id) as num')->where([
            ['create_time','>=', $sevenDaysAgo],
            ['create_time', '<', $now]
        ])->group('day')->select();

        $list =[];
        foreach($statsDayTotal as $item) {
            $list[$item->day] = $item->num;
        }

        // 设置最近7天对应的数量
        $num = [];
        foreach($day as $item) {
           $num[] = empty($list[$item]) ? 0 : $list[$item];
        }


        return [
            'total' => $total,
            'num' => $week,
            'chart'=> [
                'day' => $day,
                'num' => $num,
            ]
        ];
    }



}