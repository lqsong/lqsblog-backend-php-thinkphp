<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-PC 站点配置控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\pc\v1;

use think\Response;

use app\controller\pc\BaseController;
use app\service\pc\ConfigService;

class ConfigController extends BaseController {


    /**
     * 站点配置详情
     */
    public function configRead(): Response {
        $info = ConfigService::getAll();
        return $this->_success($info);
    }




}
