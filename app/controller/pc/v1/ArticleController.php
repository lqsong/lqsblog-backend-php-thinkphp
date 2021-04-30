<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-PC 随笔控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\pc\v1;

use think\Response;

use app\controller\pc\BaseController;
use app\service\pc\ArticleCategoryService;
use app\service\pc\ArticleService;
use enum\ResultCode;

class ArticleController extends BaseController {


    /**
     * 文章分类信息
     */
    public function articleCategory(): Response {
        $info = ArticleCategoryService::selectByAliasAndAddHit($this->request->get('alias',''));
        if(!$info) {
            return $this->_error(ResultCode::NOT_FOUND['msg'], ResultCode::NOT_FOUND['code']);
        } else {
            return $this->_success($info);
        }
    }

    /**
     * 文章列表
     */
    public function articleList(): Response {
        $list = ArticleService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
    }

    /**
     * 文章详情
     */
    public function articleDetail(): Response {
        $info = ArticleService::detailByIdAndAddHit(intval($this->request->get('id', 0)));
        if(!$info) {
            return $this->_error(ResultCode::NOT_FOUND['msg'], ResultCode::NOT_FOUND['code']);
        } else {
            return $this->_success($info);
        }
    }


    /**
     * 文章详情可能感兴趣
     */
    public function articleInterest(): Response {
        $list = ArticleService::listByIds($this->request->get('ids', ''));
        return $this->_success($list);
    }




}
