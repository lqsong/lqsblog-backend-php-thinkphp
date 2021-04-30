<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-PC 关于控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\pc\v1;

use think\Response;

use app\controller\pc\BaseController;
use app\service\pc\SinglePageService;

class AboutController extends BaseController {


    /**
     * 关于我详情
     */
    public function aboutRead(): Response {
        $info = SinglePageService::getByIdAndAddHit(1);
        return $this->_success($info);
    }




}
