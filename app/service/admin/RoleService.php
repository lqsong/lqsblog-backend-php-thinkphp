<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 角色 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\RoleModel;
use app\model\RoleResourceModel;
use app\validate\Role;
use enum\ResultCode;
use think\facade\Db;

class RoleService 
{

    /**
     * 读取所有列表
     *
     * @return array
     */
    public static function listAll(): array
    {
        return RoleModel::select()->toArray();
    }

    /**
     * 插入一条记录
     *
     * @param array $info 对应字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info = ['name'=> '', 'description'=> '', 'resources'=> '', 'resourcesLevel'=> '']): array
    {
        // 验证参数，不通过会抛出异常
        validate(Role::class)->scene('admin_service_save')->check($info);

        // 创建事务
        Db::startTrans();
        $role = [];
        try {

            $data = [
                'name' => $info['name'],            
                'description'=> empty($info['description'])?'':$info['description'],
                'resources'=> empty($info['resources'])?'':$info['resources'],
                'resourcesLevel'=> empty($info['resourcesLevel'])?'':$info['resourcesLevel'],
            ];
            $record = RoleModel::create($data);
            $role = $record->toArray();

            // 修改角色资源关联表
            self::saveBatchRoleResource($role);    

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {

            // 回滚事务
            Db::rollback();
            throw new \think\exception\ValidateException($e->getMessage());
        }       


        return $role;
    }
    
    /**
     * 根据 ID 修改
     *
     * @param array $info 对应分类字段
     * @return array
     * @throws ValidateException
     */
    public static function updateById(array $info=['id'=> 0, 'name'=> '', 'description'=> '', 'resources'=> '', 'resourcesLevel'=> '']): bool
    {
        // 验证参数，不通过会抛出异常
        validate(Role::class)->scene('admin_service_update')->check($info);

        // 创建事务
        Db::startTrans();
        try {

            $data = [
                'name' => $info['name'],            
                'description'=> empty($info['description'])?'':$info['description'],
                'resources'=> empty($info['resources'])?'':$info['resources'],
                'resourcesLevel'=> empty($info['resourcesLevel'])?'':$info['resourcesLevel'],
            ];
            $record = RoleModel::find($info['id']);
            $record->save($data);

            // 修改角色资源关联表
            self::saveBatchRoleResource($info);    
    
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
     * 批量修改角色资源关联表
     *
     * @param array $role 角色字段
     * @return array
     * @throws ValidateException
     */
    public static function saveBatchRoleResource(array $role): array
    {

        if(empty($role)) {
            throw new \think\exception\ValidateException('角色数据为空');
        }

        // 先清空同角色ID下role resource数据
        RoleResourceModel::destroy(function($query) use($role) {
            $query->where('role_id', $role['id']);
        });

        // 再批量添加
        if(!empty($role['resources'])) {
            $resourcesArr = explode(',', $role['resources']);
            $rrlist = [];
            foreach ($resourcesArr as $value) {
                $rrlist[] = [
                    'role_id' => $role['id'],
                    'resource_id'=> $value,
                ];
            }
            if(!empty($rrlist)) {
                $roleResource = new RoleResourceModel;
                return $roleResource->saveAll($rrlist)->toArray();
            }
        }

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
        try {

            RoleModel::destroy(function($query) use($id) {
                $query->where('id', $id);
            });

            RoleResourceModel::destroy(function($query) use($id) {
                $query->where('role_id', $id);
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
    


}