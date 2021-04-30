<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 专题控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\admin\v1;


use think\Response;
use think\exception\ValidateException;
use app\controller\admin\BaseController;
use app\middleware\AuthAdmin;
use app\service\admin\TopicsService;

class TopicsController  extends BaseController 
{


    protected $middleware = [
        // 专题列表 - 权限
        AuthAdmin::class . ':/admin/v1/topics:list' => ['only' => ['topicsList']],
        // 专题添加 - 权限
        AuthAdmin::class . ':/admin/v1/topics:create' => ['only' => ['topicsCreate']],
        // 专题编辑 - 权限
        AuthAdmin::class . ':/admin/v1/topics:update' => ['only' => ['topicsUpdate']],
        // 专题删除 - 权限
        AuthAdmin::class . ':/admin/v1/topics:delete' => ['only' => ['topicsDelete']],
        // 专题详情 - 权限
        AuthAdmin::class . ':/admin/v1/topics:read' => ['only' => ['topicsRead']],
    ];


    /**
     * 专题列表
     */
    public function topicsList(): Response {
        $list = TopicsService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
    }
 
    /**
     * 专题添加
     */
    public function topicsCreate(): Response {
        try {
            $info = $this->request->post();
            $info['creatorId'] = $this->currentUser['id'];
            $topics = TopicsService::save($info);
            return $this->_success(['id'=> intval($topics['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
 
    }

    /**
     * 专题编辑
     */
    public function topicsUpdate(): Response {
        try {
            TopicsService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 专题删除
     */
    public function topicsDelete(): Response {
        try {
            $bool = TopicsService::removeById($this->request->param('id/d'));
            if($bool) {
                return $this->_success();
            } else {
                return $this->_error();
            }
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 专题详情
     */
    public function topicsRead(): Response {
        try {
            $topics = TopicsService::getById($this->request->param('id/d'));
            return $this->_success($topics);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }




}
