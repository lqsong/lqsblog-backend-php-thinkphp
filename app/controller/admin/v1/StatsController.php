<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 统计控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\admin\v1;

use think\Response;
use app\controller\admin\BaseController;
use app\service\admin\ArticleService;
use app\service\admin\LinkService;
use app\service\admin\TopicsService;
use app\service\admin\WorksService;

class StatsController extends BaseController {

    protected $middleware = [
    ];

    /**
     * 随笔 - 日新增，总量，日同比，周同比
     */
    public function articlesDailyNew(): Response {
        $list = ArticleService::getArticleDailyNew();
        return $this->_success($list);
    }

    /**
     * 作品 - 周新增，总量，chart数据
     */
    public function worksWeekNew(): Response {
        $list = WorksService::getStatsTotalChart();
        return $this->_success($list);
    }

    /**
     * 专题 - 月新增，总量，chart数据
     */
    public function topicsMonthNew(): Response {
        $list = TopicsService::getStatsTotalChart();
        return $this->_success($list);
    }

    /**
     * 左邻右舍 - 年新增，总量，chart数据
     */
    public function linksAnnualNew(): Response {
        $list = LinkService::getStatsTotalChart();
        return $this->_success($list);
    }


}