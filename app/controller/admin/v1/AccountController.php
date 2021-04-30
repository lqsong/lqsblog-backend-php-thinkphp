<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 账号控制器
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
use app\service\admin\UserService;

class AccountController extends BaseController {


    protected $middleware = [
        // 账号列表 - 权限
        AuthAdmin::class . ':/admin/v1/accounts:list' => ['only' => ['accountList']],
        // 账号添加 - 权限
        AuthAdmin::class . ':/admin/v1/accounts:create' => ['only' => ['accountCreate']],
        // 账号编辑 - 权限
        AuthAdmin::class . ':/admin/v1/accounts:update' => ['only' => ['accountUpdate']],
        // 账号删除 - 权限
        AuthAdmin::class . ':/admin/v1/accounts:delete' => ['only' => ['accountDelete']],
        // 账号详情 - 权限
        AuthAdmin::class . ':/admin/v1/accounts:read' => ['only' => ['accountRead']],

        
    ];

    /**
     * 账号列表
     */
    public function accountList(): Response {
        $list = UserService::userPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
    }

    /**
     * 账号添加
     */
    public function accountCreate(): Response {
        try {
            $info = $this->request->post();
            $user = UserService::save($info);
            return $this->_success(['id'=> intval($user['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 账号编辑
     */
    public function accountUpdate(): Response {
        try {
            UserService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 账号删除
     */
    public function accountDelete(): Response {
        try {
            UserService::removeById($this->request->param('id/d'));
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 账号详情
     */
    public function accountRead(): Response {
        try {
            $user = UserService::getUserById($this->request->param('id/d'));
            return $this->_success($user);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    
}
