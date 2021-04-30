<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 标签控制器
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
use app\service\admin\TagService;

class TagController  extends BaseController 
{


    protected $middleware = [
        // 标签列表 - 权限
        AuthAdmin::class . ':/admin/v1/tags:list' => ['only' => ['tagsList']],
        // 标签添加 - 权限
        AuthAdmin::class . ':/admin/v1/tags:create' => ['only' => ['tagsCreate']],
        // 标签编辑 - 权限
        AuthAdmin::class . ':/admin/v1/tags:update' => ['only' => ['tagsUpdate']],
        // 标签删除 - 权限
        AuthAdmin::class . ':/admin/v1/tags:delete' => ['only' => ['tagsDelete']],
        // 标签搜索下拉列表 - 权限
        // AuthAdmin::class . ':/admin/v1/tags/search' => ['only' => ['tagsSearch']],
       
    ];

    /**
     * 标签列表
     */
    public function tagsList(): Response {
        $list = TagService::listPage($this->getPerPage(), $this->request->get());
        return $this->_success($list);
    }

    /**
     * 标签添加
     */
    public function tagsCreate(): Response {
        try {
            $info = $this->request->post();
            $tag = TagService::save($info);
            return $this->_success(['id'=> intval($tag['id'])]);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 标签编辑
     */
    public function tagsUpdate(): Response {
        try {
            TagService::updateById($this->request->param());
            return $this->_success();
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 标签删除
     */
    public function tagsDelete(): Response {
        try {
            $bool = TagService::removeById($this->request->param('id/d'));
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
     * 标签搜索下拉列表
     */
    public function tagsSearch(): Response {
        $list = TagService::searchKeywordsLimit($this->request->get('keywords'));
        return $this->_success($list);
    }


}
