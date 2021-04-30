<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-PC 搜索控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\pc\v1;

use think\Response;

use app\controller\pc\BaseController;
use app\service\pc\SearchHotwordLogService;
use app\service\pc\SearchHotwordService;
use app\service\pc\SearchService;

class SearchController extends BaseController {


    /**
     * 搜索
     */
    public function searchList(): Response {
        $get = $this->request->get();
        $noSid = empty($get['noSid']) ? [] : explode(',', $get['noSid']);
        $get['noSid'] = $noSid;
        
        $list = SearchService::listPage($this->getPerPage(), $get);

        // 添加Log
        SearchHotwordService::saveHotWord($this->request->get('keywords',''));
        SearchHotwordLogService::saveHotWordIp($this->request->get('keywords',''), $this->request->ip());
        
        return $this->_success($list);
    }




}
