<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 资源 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\ResourceModel;
use app\validate\Resource;
use enum\ResultCode;

class ResourceService 
{

    /**
     * 根据pId搜索列表
     *
     * @param integer $pid 父id
     * @return array
     */
    public static function listByPid(int $pid): array
    {
        if(!is_numeric($pid) || $pid < 0) {
            return [];
        }
        return ResourceModel::where('pid', $pid)->select()->toArray();
    }

    /**
     * 插入一条记录
     *
     * @param array $info 对应字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info = ['pid'=> 0, 'name'=> '', 'urlcode'=> '', 'type'=> '', 'perms'=>'', 'permsLevel'=>'']): array
    {
        // 验证参数，不通过会抛出异常
        validate(Resource::class)->scene('admin_service_save')->check($info);

        if($info['pid'] > 0) {
            $resource = ResourceModel::where('id', $info['pid'])->find();
            if(!$resource) {
                throw new \think\exception\ValidateException('父id不存在');
            }
        }

        $data = [
            'pid' => $info['pid'],
            'name' => $info['name'],            
            'urlcode'=> empty($info['urlcode'])?'':$info['urlcode'],
            'type'=> empty($info['type'])?0:$info['type'],
            'perms'=> empty($info['perms'])?'':$info['perms'],
            'permsLevel'=> empty($info['permsLevel'])?'':$info['permsLevel'],
        ];
        $record = ResourceModel::create($data);

        return $record->toArray();
    }

    /**
     * 根据 ID 修改 , pid 不修改
     *
     * @param array $info 对应分类字段
     * @return array
     * @throws ValidateException
     */
    public static function updateById(array $info=['id'=> 0,/* 'pid'=> 0,  */'name'=> '', 'urlcode'=> '', 'type'=> '', 'perms'=>'', 'permsLevel'=>'']): bool
    {
        // 验证参数，不通过会抛出异常
        validate(Resource::class)->scene('admin_service_update')->check($info);

        $data = [
            /* 'pid' => $info['pid'], */
            'name' => $info['name'],            
            'urlcode'=> empty($info['urlcode'])?'':$info['urlcode'],
            'type'=> empty($info['type'])?0:$info['type'],
            'perms'=> empty($info['perms'])?'':$info['perms'],
            'permsLevel'=> empty($info['permsLevel'])?'':$info['permsLevel'],
        ];

        $record = ResourceModel::find($info['id']);
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

        $count = ResourceModel::where('pid', $id)->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('有子元素无法删除，请先删除子元素');
        }
     
        return ResourceModel::destroy(function($query) use($id) {
                $query->where('id', $id);
        });
    }


    /**
     * 根据pid搜索 返回联动列表
     *
     * @param integer $pid 父id
     * @return array
     */
    public static function selectCascader(int $pid): array
    {
        if(!is_numeric($pid) || $pid < 0) {
            return [];
        }

        $list = ResourceModel::selectCascaderByPid($pid);

        return array_map(function($item){
            $item['leaf'] = $item['leaf'] === 1;
            return $item;
        },$list);
    }


    /**
     * 返回所有列表格式 {id,name,pid}
     *
     * @return array
     */
    public static function selectIdNamePid(): array
    {
        return ResourceModel::field('id,name,pid')->select()->toArray();
    }
    


}