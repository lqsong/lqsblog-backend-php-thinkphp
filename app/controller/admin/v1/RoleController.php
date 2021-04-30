<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 角色控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\admin\v1;

use think\Response;
use think\exception\ValidateException;
use app\controller\admin\BaseController;
use app\middleware\AuthAdmin;
use app\service\admin\RoleService;

class RoleController extends BaseController {


    protected $middleware = [
        // 角色列表 - 权限
        AuthAdmin::class . ':/admin/v1/roles:list' => ['only' => ['roleList']],
        // 角色添加 - 权限
        AuthAdmin::class . ':/admin/v1/roles:create' => ['only' => ['roleCreate']],
        // 角色编辑 - 权限
        AuthAdmin::class . ':/admin/v1/roles:update' => ['only' => ['roleUpdate']],
        // 角色删除 - 权限
        AuthAdmin::class . ':/admin/v1/roles:delete' => ['only' => ['roleDelete']],

        
    ];

    /**
     * 角色列表
     */
    public function roleList(): Response {
        $list = RoleService::listAll();
        return $this->_success($list);
    }

    /**
     * 角色添加
     */
    public function roleCreate(): Response {
        try {
            $info = $this->request->post();
            $role = RoleService::save($info);
            return $this->_success(['id'=> intval($role['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 角色编辑
     */
    public function roleUpdate(): Response {
        try {
            RoleService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 角色删除
     */
    public function roleDelete(): Response {
        try {
            RoleService::removeById($this->request->param('id/d'));
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

}
