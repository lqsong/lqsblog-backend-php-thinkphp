<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 站点配置控制器
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
use app\service\admin\ConfigService;

class ConfigController extends BaseController {


    protected $middleware = [
        // 站点配置详情 - 权限
        AuthAdmin::class . ':/admin/v1/config:read' => ['only' => ['configRead']],
        // 站点配置添加(修改) - 权限
        AuthAdmin::class . ':/admin/v1/config:update' => ['only' => ['configCreate']]
    ];

    /**
     * 站点配置详情
     */
    public function configRead(): Response {
        $info = ConfigService::getAll();
        return $this->_success($info);
}

    /**
     * 站点配置添加(修改)
     */
    public function configCreate(): Response {
        try {
            $post = $this->request->post();
            ConfigService::updateAll($post);
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        }
    }





}
