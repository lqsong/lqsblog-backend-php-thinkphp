<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-PC 专题控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\pc\v1;

use think\Response;

use app\controller\pc\BaseController;
use app\service\pc\TopicsService;
use enum\ResultCode;

class TopicsController extends BaseController {


    /**
     * 专题列表
     */
    public function topicsList(): Response {
        $list = TopicsService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
    }

    /**
     * 专题详情
     */
    public function topicsDetail(): Response {
        $info = TopicsService::detailByAliasAndAddHit($this->request->get('alias',''));
        if(!$info) {
            return $this->_error(ResultCode::NOT_FOUND['msg'], ResultCode::NOT_FOUND['code']);
        } else {
            return $this->_success($info);
        }
    }



}
