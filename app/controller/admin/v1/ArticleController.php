<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 文章控制器
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
use app\service\admin\ArticleCategoryService;
use app\service\admin\ArticleService;

class ArticleController  extends BaseController 
{


    protected $middleware = [
        // 文章列表 - 权限
        AuthAdmin::class . ':/admin/v1/articles:list' => ['only' => ['articleList']],
        // 文章添加 - 权限
        AuthAdmin::class . ':/admin/v1/articles:create' => ['only' => ['articleCreate']],
        // 文章编辑 - 权限
        AuthAdmin::class . ':/admin/v1/articles:update' => ['only' => ['articleUpdate']],
        // 文章删除 - 权限
        AuthAdmin::class . ':/admin/v1/articles:delete' => ['only' => ['articleDelete']],
        // 文章详情 - 权限
        AuthAdmin::class . ':/admin/v1/articles:read' => ['only' => ['articleRead']],
        // 文章分类列表 - 权限
        AuthAdmin::class . ':/admin/v1/articles/categorys:list' => ['only' => ['categoryList']],
        // 文章分类添加 - 权限
        AuthAdmin::class . ':/admin/v1/articles/categorys:create' => ['only' => ['categoryCreate']],
        // 文章分类编辑 - 权限
        AuthAdmin::class . ':/admin/v1/articles/categorys:update' => ['only' => ['categoryUpdate']],
        // 文章分类删除 - 权限
        AuthAdmin::class . ':/admin/v1/articles/categorys:delete' => ['only' => ['categoryDelete']],
        // 文章分类联动下拉 - 权限
        // AuthAdmin::class . ':/admin/v1/articles/categorys/cascader' => ['only' => ['categoryCascader']],
    ];


    /**
     * 文章列表
     */
    public function articleList(): Response {

        $list = ArticleService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
 
     }
 
    /**
     * 文章添加
     */
    public function articleCreate(): Response {
        try {
            $info = $this->request->post();
            $info['creatorId'] = $this->currentUser['id'];
            $article = ArticleService::save($info);
            return $this->_success(['id'=> intval($article['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
     }
 
    /**
     * 文章编辑
     */
    public function articleUpdate(): Response {
        try {
            ArticleService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
     }
 
    /**
     * 文章删除
     */
    public function articleDelete(): Response {
        try {
            $bool = ArticleService::removeById($this->request->param('id/d'));
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
     * 文章详情
     */
    public function articleRead(): Response {
        try {
            $article = ArticleService::getArticleInterestById($this->request->param('id/d'));
            return $this->_success($article);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }
 
    /**
     * 文章分类列表
     */
    public function categoryList(): Response {
        $list = ArticleCategoryService::listByPid($this->request->get('pid/d'));
        return $this->_success($list); 
    }
 
    /**
     * 文章分类添加
     */
    public function categoryCreate(): Response {
        try {
            $info = $this->request->post();
            $category = ArticleCategoryService::save($info);
            return $this->_success(['id'=> intval($category['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }
 
    /**
     * 文章分类编辑
     */
    public function categoryUpdate(): Response {
        try {
            ArticleCategoryService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }
 
    /**
     * 文章分类删除
     */
    public function categoryDelete(): Response {
        try {
            $bool = ArticleCategoryService::removeById($this->request->param('id/d'));
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
     * 文章分类联动下拉
     */
    public function categoryCascader(): Response {
        $list = ArticleCategoryService::selectCascaderByPid($this->request->get('pid/d'));
        return $this->_success($list);
    }

















}
