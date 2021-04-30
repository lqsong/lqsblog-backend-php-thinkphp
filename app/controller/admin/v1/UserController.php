<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 用户控制器
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
use app\service\admin\UserService;
use utils\BodyResult;

// use app\middleware\AuthAdmin;

class UserController extends BaseController {

    protected $middleware = [
        // AuthAdmin::class . ':/admin/v1/user/info:query' => ['only' => ['info']], // 不设置默认所有用户都有权限
        // AuthAdmin::class . ':/admin/v1/user/logout:post' => ['only' => ['logout']] // 不设置默认所有用户都有权限
    ];

    /**
     * 获取当前登录用户信息
     */
    public function info(): Response {
        try {
            $info = UserService::userInfo($this->currentUser);
            return $this->_success($info);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        }
    }


    /**
     * 退出
     */
    public function logout(): Response {
        /**
         * 1、这里后端不做操作，前端直接清空token
         * 2、如果做操作，可以结合数据库做白名单或黑名单
         */
        return BodyResult::success();
    }


}