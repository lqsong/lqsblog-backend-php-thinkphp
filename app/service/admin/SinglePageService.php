<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 单页面 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\SinglePageModel;
use enum\ResultCode;


class SinglePageService 
{

    /**
    * 根据 ID 详情
    *
    * @param integer $id
    * @return array
    * @throws ValidateException
    */
    public static function getById(int $id): array {

        if(!is_numeric($id) || $id < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        $info = SinglePageModel::where('id',$id)->find();
        if (!$info) {
            throw new \think\exception\ValidateException(ResultCode::NOT_FOUND['msg']);
        }

        return $info->toArray();
    }

    /**
     * 根据 ID 修改
     * 
    * @param array $info=['id'=> 0, 'name' => '', 'alias'=> '', 'title'=> '', 'keywords'=> '', 'description'=> '', 'content'=> '', 'hit' => 0] 对应字段
    * @return bool
    * @throws ValidateException
     */
    public static function updateById(array $info=['id'=> 0, 'name' => '', 'alias'=> '', 'title'=> '', 'keywords'=> '', 'description'=> '', 'content'=> '', 'hit' => 0]): bool {
       
        if(!is_numeric($info['id']) || $info['id'] < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        $singlePage = SinglePageModel::find($info['id']);
        if(!$singlePage) {
            throw new \think\exception\ValidateException(ResultCode::NOT_FOUND['msg']);
        }

        $singlePage->title = $info['title'];
        $singlePage->keywords = $info['keywords'];
        $singlePage->description = $info['description'];
        $singlePage->content = $info['content'];
        if(!empty($info['name'])) {
            $singlePage->name = $info['name'];
        }
        if(!empty($info['alias'])) {
            $singlePage->alias = $info['alias'];
        }
        if(!empty($info['hit'])) {
            $singlePage->hit = $info['hit'];
        }

        return $singlePage->save();
    }

}