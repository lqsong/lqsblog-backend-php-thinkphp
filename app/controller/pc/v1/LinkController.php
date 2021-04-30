<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-PC 左邻右舍控制器
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
use app\service\pc\LinkService;

class LinkController extends BaseController {


    /**
     * 左邻右舍列表
     */
    public function linksList(): Response {
        $list = LinkService::selectLinkCategoryAll();
        return $this->_success($list);
    }

    /**
     * 左邻右舍推荐
     */
    public function linksRecommend(): Response {
        $list = LinkService::getByCategoryIds($this->request->get('ids',''));
        return $this->_success($list);
    }


}
