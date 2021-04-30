<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 作品控制器
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
use app\service\admin\WorksService;

class WorksController  extends BaseController 
{


    protected $middleware = [
        // 作品列表 - 权限
        AuthAdmin::class . ':/admin/v1/works:list' => ['only' => ['worksList']],
        // 作品添加 - 权限
        AuthAdmin::class . ':/admin/v1/works:create' => ['only' => ['worksCreate']],
        // 作品编辑 - 权限
        AuthAdmin::class . ':/admin/v1/works:update' => ['only' => ['worksUpdate']],
        // 作品删除 - 权限
        AuthAdmin::class . ':/admin/v1/works:delete' => ['only' => ['worksDelete']],
        // 作品详情 - 权限
        AuthAdmin::class . ':/admin/v1/works:read' => ['only' => ['worksRead']],
    ];


    /**
     * 作品列表
     */
    public function worksList(): Response {

        $list = WorksService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
 
    }
 
    /**
     * 作品添加
     */
    public function worksCreate(): Response {
        try {
            $info = $this->request->post();
            $info['creatorId'] = $this->currentUser['id'];
            $works = WorksService::save($info);
            return $this->_success(['id'=> intval($works['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 作品编辑
     */
    public function worksUpdate(): Response {
        try {
            WorksService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 作品删除
     */
    public function worksDelete(): Response {
        try {
            $bool = WorksService::removeById($this->request->param('id/d'));
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
     * 作品详情
     */
    public function worksRead(): Response {
        try {
            $works = WorksService::getById($this->request->param('id/d'));
            return $this->_success($works);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }




}
