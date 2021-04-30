<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 左邻右舍 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\LinkCategoryModel;
use app\model\LinkModel;
use app\validate\Link;
use enum\ResultCode;

class LinkService 
{

    /**
     * 获取排序字段
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getSort(int $i): string 
    {
        $sort = [ 'id', 'hit' ];
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
     * @param array $search= [ 'keywords'=> '', 'categoryid'=> '', 'sort'=> 排序字段下标[id,hit,addtime], 'order'=> 下标[desc 降序，asc 升序]] 搜索字段
     * @return array
     */
    public static function listPage(array $paginate=['per'=>10, 'page'=>1 ], array $search= [
        'keywords'=> '', 'categoryid'=> '', 'sort'=> 0, 'order'=> 0
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

        
        $total = LinkModel::where($where)->count();
        $link = LinkModel::where($where)->order(self::getSort($search['sort']),  self::getOrderType($search['order']))->page($paginate['page'], $paginate['per'])->select();        
        $response = [
            'total' => $total,
            'currentPage' => $paginate['page'],
            'list' => []
        ];
        if($link->isEmpty()) {
            return $response;
        }

        // 取出分类id
        $cIds = [];
        foreach($link as $item){
            $cIds[] = $item->categoryId;
        }
        $linkCategories = LinkCategoryModel::where('id', 'in', $cIds)->select();
        $categorys = [];
        foreach($linkCategories as $item) {
            $categorys[$item->id] = [
                'id'=> $item->id,
                'name' => $item->name,
                'alias' => $item->alias,
            ];
        }

        // 设置返回数据列表
        $list = [];
        foreach($link as $item){
            $list[] = [
                'id' => $item->id,
                'title' => $item->title,
                'category' => !empty($categorys[$item->categoryId]) ? $categorys[$item->categoryId] : [],
                'hit' => $item->hit,
            ];
        }

        $response['list'] = $list;
        return $response; 

    }


    /**
     * 插入一条记录
     *
     * @param array $info 对应字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info=[
        'title'=> '', 'description'=> '', 'categoryId'=> '', 'logo'=> '', 'href'=> '', 'hit'=> '', 'creatorId'=> ''
    ]): array
    {
        // 验证参数，不通过会抛出异常
        validate(Link::class)->scene('admin_service_save')->check($info);

        $data = [
            'title'=> $info['title'],
            'description'=> empty($info['description'])?'':$info['description'],
            'categoryId'=> $info['categoryId'],
            'logo'=> empty($info['logo'])?'':$info['logo'],
            'href'=> empty($info['href'])?'':$info['href'],
            'hit'=> empty($info['hit'])?0:$info['hit'],
            'creator_id'=> empty($info['creatorId'])?0:$info['creatorId'],
            'create_time'=> date("Y-m-d H:i:s"),
        ];
        $record = LinkModel::create($data);

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
        'id'=>0, 'title'=> '', 'description'=> '', 'categoryId'=> '', 'logo'=> '', 'href'=> '', 'hit'=> '', 'creatorId'=> ''
    ]): bool
    {
        // 验证参数，不通过会抛出异常
        validate(Link::class)->scene('admin_service_update')->check($info);
        
        $data = [
            'title'=> $info['title'],
            'description'=> empty($info['description'])?'':$info['description'],
            'categoryId'=> $info['categoryId'],
            'logo'=> empty($info['logo'])?'':$info['logo'],
            'href'=> empty($info['href'])?'':$info['href'],
        ];
        if(!empty($info['hit'])) {
            $data['hit'] = $info['hit'];
        }

        $record = LinkModel::find($info['id']);
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

        return LinkModel::destroy(function($query) use($id) {
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
    public static function getLinkById(int $id): array
    {
        if(!is_numeric($id) || $id < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        $link = LinkModel::find($id);
        if(!$link) {
            throw new \think\exception\ValidateException(ResultCode::NOT_FOUND['msg']);
        }

        return $link->toArray();
    }


    /**
     * 统计 - 年新增，总量，chart数据
     *
     * @return array
     */
    public static function getStatsTotalChart(): array
    {

        // 获取当前年
        $nowYear = date("Y");
        // 去年
        $nowYearBefore = $nowYear-1;
        // 明年
        $nowYearAfter = $nowYear+1;
        // 获取当前月
        $nowMonthValue = date("m");

        // 今年1月1日
        $year = $nowYear .  '-01-01';
        // 今年当月1日
        $yearMonth = $nowYear . '-' . $nowMonthValue . '-01';
        // 明年1月1日
        $yearAfter = $nowYearAfter . '-01-01';
        // 12 月前 1号日期
        $twelveMothsDays = $nowYearBefore . '-' . $nowMonthValue . '-01';

        // 总数
        $total = LinkModel::count();

        // 本年新增
        $yearCount = LinkModel::where([
            ['create_time','>=', $year],
            ['create_time', '<', $yearAfter]
        ])->count();

        // 设置最近12月数组
        $day = [];
        for ($index = 12; $index > 0; $index--) {
           $day[] = date('Y-m',strtotime('-'. $index .' month'));
        }

        // 读取最近12月数据
        $statsDayTotal = LinkModel::field('DATE_FORMAT(create_time,"%Y-%m") as day,COUNT(id) as num')->where([
            ['create_time','>=', $twelveMothsDays],
            ['create_time', '<', $yearMonth]
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
            'num' => $yearCount,
            'chart'=> [
                'day' => $day,
                'num' => $num,
            ]
        ];
    }


}