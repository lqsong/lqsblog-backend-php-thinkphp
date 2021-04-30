<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 菜单控制器
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
use app\service\admin\ResourceService;

class MenuController extends BaseController {


    protected $middleware = [
        // 菜单列表 - 权限
        AuthAdmin::class . ':/admin/v1/menus:list' => ['only' => ['menuList']],
        // 菜单添加 - 权限
        AuthAdmin::class . ':/admin/v1/menus:create' => ['only' => ['menuCreate']],
        // 菜单编辑 - 权限
        AuthAdmin::class . ':/admin/v1/menus:update' => ['only' => ['menuUpdate']],
        // 菜单删除 - 权限
        AuthAdmin::class . ':/admin/v1/menus:delete' => ['only' => ['menuDelete']],
        // 菜单联动下拉 - 权限
        // AuthAdmin::class . ':/admin/v1/menus/cascader:list' => ['only' => ['menuCascader']],
        // 菜单列表 - 全部 - 权限
        // AuthAdmin::class . ':/admin/v1/menus/all:list' => ['only' => ['menuListAll']],
        
    ];

    /**
     * 菜单列表
     */
    public function menuList(): Response {
        $list = ResourceService::listByPid($this->request->get('pid/d'));
        return $this->_success($list); 
    }

    /**
     * 菜单添加
     */
    public function menuCreate(): Response {
        try {
            $info = $this->request->post();
            $resource = ResourceService::save($info);
            return $this->_success(['id'=> intval($resource['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 菜单编辑
     */
    public function menuUpdate(): Response {
        try {
            ResourceService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 菜单删除
     */
    public function menuDelete(): Response {
        try {
            $bool = ResourceService::removeById($this->request->param('id/d'));
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
     * 菜单联动下拉
     */
    public function menuCascader(): Response {
        $list = ResourceService::selectCascader($this->request->get('pid/d'));
        return $this->_success($list);
    }

    /**
     * 菜单列表 - 全部
     */
    public function menuListAll(): Response {
        $list = ResourceService::selectIdNamePid();
        return $this->_success($list);
    }








}
