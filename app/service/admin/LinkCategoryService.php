<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 左邻右舍分类 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\LinkCategoryModel;
use app\model\LinkModel;
use app\validate\LinkCategory;
use enum\ResultCode;

class LinkCategoryService 
{

    /**
     * 获取排序字段
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getSort(int $i): string 
    {
        $sort = [ 'id', 'sort' ];
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
     * 获取列表信息
     *
     * @param integer $sort 排序字段下标[id,hit]
     * @param integer $order 下标[desc 降序，asc 升序]
     * @return array
     */
    public static function list(int $sort, int $order): array
    {
        return LinkCategoryModel::order(self::getSort($sort),  self::getOrderType($order))->select()->toArray();     
    }

    /**
     * 插入一条记录
     *
     * @param array $info 对应字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info=['name'=> '', 'alias'=> '', 'sort'=> 0]): array
    {
        // 验证参数，不通过会抛出异常
        validate(LinkCategory::class)->scene('admin_service_save')->check($info);

        $count = LinkCategoryModel::where('alias', $info['alias'])->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('存在相同别名');
        }

        $data = [
            'name'=> $info['name'],
            'alias'=> $info['alias'],
            'sort'=> empty($info['sort'])?0:$info['sort'],
        ];
        $record = LinkCategoryModel::create($data);

        return $record->toArray();
    }


    /**
     * 根据 ID 修改
     *
     * @param array $info 对应字段
     * @return boolean
     * @throws ValidateException
     */
    public static function updateById(array $info=['id'=> 0,'name'=> '', 'alias'=> '', 'sort'=> 0]): bool
    {
        // 验证参数，不通过会抛出异常
        validate(LinkCategory::class)->scene('admin_service_update')->check($info);

        $count = LinkCategoryModel::where([
            ['alias', '=', $info['alias']],
            ['id', '<>', $info['id']]
        ])->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('存在相同别名');
        }

        $data = [
            'name'=> $info['name'],
            'alias'=> $info['alias'],
            'sort'=> empty($info['sort'])?0:$info['sort'],
        ];

        $record = LinkCategoryModel::find($info['id']);
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

        $aCount = LinkModel::where('category_id', $id)->count();
        if($aCount > 0) {
            throw new \think\exception\ValidateException('分类下有数据，请先删除分类下数据');
        }

        return LinkCategoryModel::destroy(function($query) use($id) {
            $query->where('id', $id);
        });
    }

    




}