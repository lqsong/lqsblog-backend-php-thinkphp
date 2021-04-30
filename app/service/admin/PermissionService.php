<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 权限 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\PermissionModel;
use enum\ResultCode;
use app\model\ResourcePermissionModel;
use app\model\UserRoleModel;
use app\validate\Permission;

class PermissionService 
{


    /**
     * 根据用户id获取权限列表
     *
     * @param integer $userId  用户id
     * @return array
     * @throws ValidateException
     */
    public static function listPermissionByUserId(int $userId): array 
    {
        if(!is_numeric($userId) || $userId < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        // 获取资源
        $resourceIds=[];
        $userRole = UserRoleModel::with(['roleResource'])->where(['user_id' => $userId])->select();
        foreach($userRole as $item){
            $resourceIds = array_merge($resourceIds, $item->roleResource->column('resource_id'));
        }
        
        // 获取资源对应的权限
        $perms = [];
        $resourcePermission = ResourcePermissionModel::with(['permission'])->where('resource_id', 'in', $resourceIds)->select();
        foreach($resourcePermission as $item){
            $perms[] = $item->permission->permission;
        }

        return $perms;
    }

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
        return PermissionModel::where('pid', $pid)->select()->toArray();
    }

    /**
     * 插入一条记录
     *
     * @param array $info 对应字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info = ['pid'=> 0, 'name'=> '', 'permission'=> '', 'description'=> '']): array
    {
        // 验证参数，不通过会抛出异常
        validate(Permission::class)->scene('admin_service_save')->check($info);

        if($info['pid'] > 0) {
            $permission = PermissionModel::where('id', $info['pid'])->find();
            if(!$permission) {
                throw new \think\exception\ValidateException('父id不存在');
            }
        }

        $data = [
            'pid' => $info['pid'],
            'name' => $info['name'],            
            'permission'=> empty($info['permission'])?'':$info['permission'],
            'description'=> empty($info['description'])?'':$info['description'],
        ];
        $record = PermissionModel::create($data);

        return $record->toArray();
    }

    /**
     * 根据 ID 修改 , pid 不修改
     *
     * @param array $info 对应分类字段
     * @return array
     * @throws ValidateException
     */
    public static function updateById(array $info=['id'=> 0,/* 'pid'=> 0,  */'name'=> '', 'permission'=> '', 'description'=> '']): bool
    {
        // 验证参数，不通过会抛出异常
        validate(Permission::class)->scene('admin_service_update')->check($info);


        $data = [
            /* 'pid' => $info['pid'], */
            'name' => $info['name'],            
            'permission'=> empty($info['permission'])?'':$info['permission'],
            'description'=> empty($info['description'])?'':$info['description'],
        ];

        $record = PermissionModel::find($info['id']);
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

        $count = PermissionModel::where('pid', $id)->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('有子元素无法删除，请先删除子元素');
        }
     
        return PermissionModel::destroy(function($query) use($id) {
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

        $list = PermissionModel::selectCascaderByPid($pid);

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
        return PermissionModel::field('id,name,pid')->select()->toArray();
    }

}