<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 上传控制器
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
use app\service\admin\AttachmentService;

// use app\middleware\AuthAdmin;

class UploadController extends BaseController {

    protected $middleware = [
        // AuthAdmin::class . ':/admin/v1/user/upload:list' => ['only' => ['imagesList']], 
        // AuthAdmin::class . ':/admin/v1/user/upload:create' => ['only' => ['imagesCreate']],
        // AuthAdmin::class . ':/admin/v1/user/upload:down' => ['only' => ['imagesDown']],
    ];

    /**
     * 图片列表
     */
    public function imagesList(): Response {
        $list = AttachmentService::listPage($this->getPerPage());
        return $this->_success($list);
    }

    /**
     * 图片上传
     */
    public function imagesCreate(): Response {
        try {
            $info = AttachmentService::imgSave('file', $this->currentUser['id']);
            return $this->_success($info);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }

    /**
     * 图片下载
     */
    public function imagesDown(): Response {
        try {
            $info = AttAchmentService::getImgById($this->request->param('id/d'));
            return download($info['filePath'], $info['fileName']);
        } catch (ValidateException $e) {
            return $this->_error($e->getError());
        } 
    }


}