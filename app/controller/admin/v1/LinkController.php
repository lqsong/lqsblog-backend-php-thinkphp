<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 左邻右舍控制器
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
use app\service\admin\LinkCategoryService;
use app\service\admin\LinkService;

class LinkController  extends BaseController 
{


    protected $middleware = [
        // 左邻右舍列表 - 权限
        AuthAdmin::class . ':/admin/v1/links:list' => ['only' => ['linkList']],
        // 左邻右舍添加 - 权限
        AuthAdmin::class . ':/admin/v1/links:create' => ['only' => ['linkCreate']],
        // 左邻右舍编辑 - 权限
        AuthAdmin::class . ':/admin/v1/links:update' => ['only' => ['linkUpdate']],
        // 左邻右舍删除 - 权限
        AuthAdmin::class . ':/admin/v1/links:delete' => ['only' => ['linkDelete']],
        // 左邻右舍详情 - 权限
        AuthAdmin::class . ':/admin/v1/links:read' => ['only' => ['linkRead']],
        // 左邻右舍分类列表 - 权限
        AuthAdmin::class . ':/admin/v1/links/categorys:list' => ['only' => ['categoryList']],
        // 左邻右舍分类添加 - 权限
        AuthAdmin::class . ':/admin/v1/links/categorys:create' => ['only' => ['categoryCreate']],
        // 左邻右舍分类编辑 - 权限
        AuthAdmin::class . ':/admin/v1/links/categorys:update' => ['only' => ['categoryUpdate']],
        // 左邻右舍分类删除 - 权限
        AuthAdmin::class . ':/admin/v1/links/categorys:delete' => ['only' => ['categoryDelete']],

    ];


    /**
     * 左邻右舍列表
     */
    public function linkList(): Response {
        $list = LinkService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
    }

    /**
     * 左邻右舍添加
     */
    public function linkCreate(): Response {
        try {
            $info = $this->request->post();
            $info['creatorId'] = $this->currentUser['id'];
            $link = LinkService::save($info);
            return $this->_success(['id'=> intval($link['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }
 
    /**
     * 左邻右舍编辑
     */
    public function linkUpdate(): Response {
        try {
            LinkService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 左邻右舍删除
     */
    public function linkDelete(): Response {
        try {
            $bool = LinkService::removeById($this->request->param('id/d'));
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
     * 左邻右舍详情
     */
    public function linkRead(): Response {
        try {
            $link = LinkService::getLinkById($this->request->param('id/d'));
            return $this->_success($link);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 左邻右舍分类列表
     */
    public function categoryList(): Response {
        $list = LinkCategoryService::list(1,1);
        return $this->_success($list); 
    }

    /**
     * 左邻右舍分类添加
     */
    public function categoryCreate(): Response {
        try {
            $info = $this->request->post();
            $category = LinkCategoryService::save($info);
            return $this->_success(['id'=> intval($category['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 左邻右舍分类编辑
     */
    public function categoryUpdate(): Response {
        try {
            LinkCategoryService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 左邻右舍分类删除
     */
    public function categoryDelete(): Response {
        try {
            $bool = LinkCategoryService::removeById($this->request->param('id/d'));
            if($bool) {
                return $this->_success();
            } else {
                return $this->_error();
            }
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }


}
