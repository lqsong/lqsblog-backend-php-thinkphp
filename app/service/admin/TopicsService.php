<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 专题 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\TopicsModel;
use app\validate\Topics;
use enum\ResultCode;

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


        $total = TopicsModel::where($where)->count();
        $topics = TopicsModel::where($where)->order(self::getSort($search['sort']),  self::getOrderType($search['order']))->page($paginate['page'], $paginate['per'])->select();        
        return [
            'total' => $total,
            'currentPage' => $paginate['page'],
            'list' => $topics->hidden(['content'])->toArray()
        ];

    }

    /**
     * 插入一条记录
     *
     * @param array $info 对应字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info=[
        'title'=> '', 'keywords'=> '', 'description'=> '', 'alias'=> '', 'content'=> '', 'addtime'=> '', 'hit'=> '', 'creatorId'=> ''
    ]): array
    {

        // 验证参数，不通过会抛出异常
        validate(Topics::class)->scene('admin_service_save')->check($info);

        $count = TopicsModel::where('alias', $info['alias'])->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('存在相同别名');
        }

        $data = [
            'title'=> empty($info['title'])?'':$info['title'],
            'keywords'=> empty($info['keywords'])?'':$info['keywords'],
            'description'=> empty($info['description'])?'':$info['description'],
            'alias'=> $info['alias'],
            'content'=> empty($info['content'])?'':$info['content'],
            'addtime'=> $info['addtime'],
            'hit'=> empty($info['hit'])?0:$info['hit'],
            'creator_id'=> empty($info['creatorId'])?0:$info['creatorId'],
            'create_time'=> date("Y-m-d H:i:s"),
        ];
        $record = TopicsModel::create($data);

        return $record->toArray();
    }

    /**
     * 根据 ID 修改
     *
     * @param array $info 对应字段
     * @return boolean
     * @throws ValidateException
     */
    public static function updateById(array $info=[
        'id'=> 0,'title'=> '', 'keywords'=> '', 'description'=> '', 'alias'=> '', 'content'=> '', 'addtime'=> '', 'hit'=> ''
    ]): bool
    {

        // 验证参数，不通过会抛出异常
        validate(Topics::class)->scene('admin_service_update')->check($info);

        $count = TopicsModel::where([
            ['alias', '=', $info['alias']],
            ['id', '<>', $info['id']]
        ])->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('存在相同别名');
        }

        $data = [
            'title'=> empty($info['title'])?'':$info['title'],
            'keywords'=> empty($info['keywords'])?'':$info['keywords'],
            'description'=> empty($info['description'])?'':$info['description'],
            'alias'=> $info['alias'],
            'content'=> empty($info['content'])?'':$info['content'],
            'addtime'=> $info['addtime'],
        ];
        if(!empty($info['hit'])) {
            $data['hit'] = $info['hit'];
        }

        $record = TopicsModel::find($info['id']);
        return $record->save($data);
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

        return TopicsModel::destroy(function($query) use($id) {
            $query->where('id', $id);
        });
    
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

        $topics = TopicsModel::find($id);
        if(!$topics) {
            throw new \think\exception\ValidateException(ResultCode::NOT_FOUND['msg']);
        }

        return $topics->toArray();
    }


    /**
     * 统计 - 月新增，总量，chart数据
     *
     * @return array
     */
    public static function getStatsTotalChart(): array
    {

        // 当前日期
        $now = date("Y-m-d");
        // 后一天日期
        $dayAfter = date("Y-m-d",strtotime("+1 day"));
        // 获取本月1号日期
        $monday = date('Y-m', time()) . '-01';

        // 总数
        $total = TopicsModel::count();

        // 本月新增
        $month = TopicsModel::where([
            ['create_time','>=', $monday],
            ['create_time', '<', $dayAfter]
        ])->count();

        // 设置最近30天数组
        $day = [];
        for ($index = 30; $index > 0; $index--) {
           $day[] = date('Y-m-d',strtotime('-'. $index .' day'));
        }

        // 读取最近30天数据
        $statsDayTotal = TopicsModel::field('DATE_FORMAT(create_time,"%Y-%m-%d") as day,COUNT(id) as num')->where([
            ['create_time','>=', $day[0]],
            ['create_time', '<', $now]
        ])->group('day')->select();

        $list =[];
        foreach($statsDayTotal as $item) {
            $list[$item->day] = $item->num;
        }

        // 设置最近30天对应的数量
        $num = [];
        foreach($day as $item) {
           $num[] = empty($list[$item]) ? 0 : $list[$item];
        }


        return [
            'total' => $total,
            'num' => $month,
            'chart'=> [
                'day' => $day,
                'num' => $num,
            ]
        ];
    }




}