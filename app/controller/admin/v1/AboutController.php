<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 关于控制器
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
use app\service\admin\SinglePageService;
use app\middleware\AuthAdmin;

class AboutController extends BaseController {


    protected $middleware = [
        // 关于我详情 - 权限
        AuthAdmin::class . ':/admin/v1/about:read' => ['only' => ['aboutRead']],
        // 关于我添加(修改) - 权限
        AuthAdmin::class . ':/admin/v1/about:update' => ['only' => ['aboutCreate']]
    ];

    /**
     * 关于我详情
     */
    public function aboutRead(): Response {
        try {
            $info = SinglePageService::getById(1);
            return $this->_success($info);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        }
        
    }


    /**
     * 关于我添加(修改)
     */
    public function aboutCreate(): Response {
        try {
            $post = $this->request->post();
            $post['id'] = 1;
            SinglePageService::updateById($post);
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        }
    }



}
