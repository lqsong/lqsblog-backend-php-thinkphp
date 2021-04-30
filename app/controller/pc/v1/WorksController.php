<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-PC 作品控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\pc\v1;

use think\Response;
use app\controller\pc\BaseController;
use app\service\pc\WorksService;
use enum\ResultCode;

class WorksController extends BaseController {


    /**
     * 作品列表
     */
    public function worksList(): Response {
        $list = WorksService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
    }

    /**
     * 作品详情
     */
    public function worksDetail(): Response {
        $info = WorksService::detailByIdAndAddHit(intval($this->request->get('id', 0)));
        if(!$info) {
            return $this->_error(ResultCode::NOT_FOUND['msg'], ResultCode::NOT_FOUND['code']);
        } else {
            return $this->_success($info);
        }
    }


}
