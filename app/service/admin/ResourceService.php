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
use app\model\ResourcePermissionModel;
use app\validate\Resource;
use enum\ResultCode;
use think\facade\Db;

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

        // 创建事务
        Db::startTrans();
        $resource = [];
        try {
            $data = [
                'pid' => $info['pid'],
                'name' => $info['name'],            
                'urlcode'=> empty($info['urlcode'])?'':$info['urlcode'],
                'type'=> empty($info['type'])?0:$info['type'],
                'perms'=> empty($info['perms'])?'':$info['perms'],
                'permsLevel'=> empty($info['permsLevel'])?'':$info['permsLevel'],
            ];
            $record = ResourceModel::create($data);
            $resource = $record->toArray();

            // 修改资源权限关联表
            self::saveBatchResourcePermission($resource);    

            // 提交事务
            Db::commit();
    
        } catch (\Exception $e) {

            // 回滚事务
            Db::rollback();
            throw new \think\exception\ValidateException($e->getMessage());
        } 

        return $resource;
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

        // 创建事务
        Db::startTrans();
        try {

            $data = [
                /* 'pid' => $info['pid'], */
                'name' => $info['name'],            
                'urlcode'=> empty($info['urlcode'])?'':$info['urlcode'],
                'type'=> empty($info['type'])?0:$info['type'],
                'perms'=> empty($info['perms'])?'':$info['perms'],
                'permsLevel'=> empty($info['permsLevel'])?'':$info['permsLevel'],
            ];
    
            $record = ResourceModel::find($info['id']);
            $record->save($data);

            // 修改资源权限关联表
            self::saveBatchResourcePermission($info);    
    
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
     * 批量修改资源权限关联表
     *
     * @param array $resource 资源字段
     * @return array
     * @throws ValidateException
     */
    public static function saveBatchResourcePermission(array $resource): array
    {
        if(empty($resource)) {
            throw new \think\exception\ValidateException('资源数据为空');
        }

        // 先清空同资源ID下resource permission数据
        ResourcePermissionModel::destroy(function($query) use($resource) {
            $query->where('resource_id', $resource['id']);
        });

        $res = [];
        // 再批量添加
        if(!empty($resource['perms'])) {
            $permsArr = explode(',', $resource['perms']);
            $rplist = [];
            foreach ($permsArr as $value) {
                $rplist[] = [
                    'resource_id' => $resource['id'],
                    'permission_id'=> $value,
                ];
            }
            if(!empty($rplist)) {
                $resourcePermission = new ResourcePermissionModel;
                $res = $resourcePermission->saveAll($rplist)->toArray();
            }
        }

        return $res;
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

        // 创建事务
        Db::startTrans();
        try {

            ResourceModel::destroy(function($query) use($id) {
                $query->where('id', $id);
            });

            ResourcePermissionModel::destroy(function($query) use($id) {
                $query->where('resource_id', $id);
            });

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