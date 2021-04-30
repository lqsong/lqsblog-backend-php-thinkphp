<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-PC 首页控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\pc\v1;

use think\Response;

use app\controller\pc\BaseController;
use app\service\pc\SearchService;

class HomeController extends BaseController {


    /**
     * 首页推荐
     */
    public function indexRecommend(): Response {
        $list = SearchService::getRecommend(5);
        return $this->_success($list);
    }

    /**
     * 首页列表
     */
    public function indexList(): Response {
        $get = $this->request->get();
        $noSid = empty($get['noSid']) ? [] : explode(',', $get['noSid']);
        $get['noSid'] = $noSid;
        
        $list = SearchService::listPage($this->getPerPage(), $get);
        return $this->_success($list);
    }


}
