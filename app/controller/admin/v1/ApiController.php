<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin API控制器
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
use app\service\admin\PermissionService;

class ApiController extends BaseController {


    protected $middleware = [
        // API列表 - 权限
        AuthAdmin::class . ':/admin/v1/apis:list' => ['only' => ['apiList']],
        // API添加 - 权限
        AuthAdmin::class . ':/admin/v1/apis:create' => ['only' => ['apiCreate']],
        // API编辑 - 权限
        AuthAdmin::class . ':/admin/v1/apis:update' => ['only' => ['apiUpdate']],
        // API删除 - 权限
        AuthAdmin::class . ':/admin/v1/apis:delete' => ['only' => ['apiDelete']],
        // API联动下拉 - 权限
        // AuthAdmin::class . ':/admin/v1/apis/cascader:list' => ['only' => ['apiCascader']],
        // API列表 - 全部 - 权限
        // AuthAdmin::class . ':/admin/v1/apis/all:list' => ['only' => ['apiListAll']],        
    ];

    /**
     * API列表
     */
    public function apiList(): Response {
        $list = PermissionService::listByPid($this->request->get('pid/d'));
        return $this->_success($list); 
    }

    /**
     * API添加
     */
    public function apiCreate(): Response {
        try {
            $info = $this->request->post();
            $permission = PermissionService::save($info);
            return $this->_success(['id'=> intval($permission['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * API编辑
     */
    public function apiUpdate(): Response {
        try {
            PermissionService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * API删除
     */
    public function apiDelete(): Response {
        try {
            $bool = PermissionService::removeById($this->request->param('id/d'));
            if($bool) {
                return $this->_success();
            } else {
                return $this->_error();
            }
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * API联动下拉
     */
    public function apiCascader(): Response {
        $list = PermissionService::selectCascader($this->request->get('pid/d'));
        return $this->_success($list);
    }

    /**
     * API列表 - 全部
     */
    public function apiListAll(): Response {
        $list = PermissionService::selectIdNamePid();
        return $this->_success($list);
    }


}
