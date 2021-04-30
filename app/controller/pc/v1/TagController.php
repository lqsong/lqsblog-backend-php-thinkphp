<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-PC 标签控制器
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
use app\service\pc\TagLogService;
use app\service\pc\TagService;
use enum\ResultCode;

class TagController extends BaseController {


    /**
     * 标签下内容列表
     */
    public function tagList(): Response {
        $name = $this->request->get('name','');
        if(empty($name)) {
            return $this->_success();
        }
               
        $list = SearchService::listPage($this->getPerPage(), ['tag'=> $name]);

        // 添加Log
        TagLogService::saveTagIp($name, $this->request->ip());

        return $this->_success($list);

    }


    /**
     * 标签详情
     */
    public function tagDetail(): Response {
        $info = TagService::getByNameAndAddHit($this->request->get('name',''));
        if(!$info) {
            return $this->_error(ResultCode::NOT_FOUND['msg'], ResultCode::NOT_FOUND['code']);
        } else {
            return $this->_success($info);
        }
    }


}
