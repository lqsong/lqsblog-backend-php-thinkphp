<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 搜索控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\admin\v1;

use think\Response;
use app\controller\admin\BaseController;
use app\service\admin\SearchHotwordService;
use app\service\admin\SearchService;

class SearchController extends BaseController {

    protected $middleware = [
    ];

    /**
     * 搜索列表
     */
    public function searchList(): Response {
        $list = SearchService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
    }

    /**
     * 搜索热门关键词列表
     */
    public function keywordsList(): Response {
        $list = SearchHotwordService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
    }



}