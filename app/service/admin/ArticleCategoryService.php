<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 文章分类 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\ArticleCategoryModel;
use app\model\ArticleModel;
use app\validate\ArticleCategory;
use enum\ResultCode;

class ArticleCategoryService 
{

    /**
     * 根据pId搜索列表
     *
     * @param integer $pId 父id
     * @return array
     */
    public static function listByPid(int $pId): array
    {

        if(!is_numeric($pId) || $pId < 0) {
            return [];
        }
        return ArticleCategoryModel::where('p_id', $pId)->select()->toArray();

    }


    /**
     * 插入一条记录
     *
     * @param array $info 对应分类字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info= [
        'pid'=> 0, 'name'=> '', 'alias'=> '', 'title'=> '', 'keywords'=> '', 'description'=> '', 'hit'=> 0
    ]): array
    {
        // 验证参数，不通过会抛出异常
        validate(ArticleCategory::class)->scene('admin_service_save')->check($info);

        if($info['pid'] > 0) {
            $pCategory = ArticleCategoryModel::where('id', $info['pid'])->find();
            if(!$pCategory) {
                throw new \think\exception\ValidateException('父id不存在');
            }
        }

        $count = ArticleCategoryModel::where('alias', $info['alias'])->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('存在相同别名');
        }

        $data = [
            'p_id' => $info['pid'],
            'name' => $info['name'],            
            'alias'=> $info['alias'],
            'title'=> empty($info['title'])?'':$info['title'],
            'keywords'=> empty($info['keywords'])?'':$info['keywords'],
            'description'=> empty($info['description'])?'':$info['description'],
            'hit'=> empty($info['hit'])?0:$info['hit'],
        ];
        $record = ArticleCategoryModel::create($data);

        return $record->toArray();
    }


    /**
     * 根据 ID 修改 , pid 不修改
     *
     * @param array $info 对应分类字段
     * @return array
     * @throws ValidateException
     */
    public static function updateById(array $info= [
        'id'=>0, /* 'pid'=> 0, */ 'name'=> '', 'alias'=> '', 'title'=> '', 'keywords'=> '', 'description'=> '', 'hit'=> 0
    ]): bool
    {

        // 验证参数，不通过会抛出异常
        validate(ArticleCategory::class)->scene('admin_service_update')->check($info);

        $count = ArticleCategoryModel::where([
            ['alias', '=', $info['alias']],
            ['id', '<>', $info['id']]
        ])->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('存在相同别名');
        }

        $data = [
            // 'p_id' => $info['pid'],
            'name' => $info['name'],            
            'alias'=> $info['alias'],
            'title'=> empty($info['title'])?'':$info['title'],
            'keywords'=> empty($info['keywords'])?'':$info['keywords'],
            'description'=> empty($info['description'])?'':$info['description'],
        ];
        if(!empty($info['hit'])) {
            $data['hit'] = $info['hit'];
        }

        $record = ArticleCategoryModel::find($info['id']);
        return $record->save($data);
    }

    /**
     * 根据 ID 删除
     *
     * @param integer $id 分类id
     * @return boolean
     * @throws ValidateException
     */
    public static function removeById(int $id): bool
    {

        if(!is_numeric($id) || $id < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        $count = ArticleCategoryModel::where('p_id', $id)->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('有子元素无法删除，请先删除子元素');
        }

        $aCount = ArticleModel::where('category_id', $id)->count();
        if($aCount > 0) {
            throw new \think\exception\ValidateException('分类下有数据，请先删除分类下数据');
        }

        return ArticleCategoryModel::destroy(function($query) use($id) {
                $query->where('id', $id);
        });
    }

    /**
     * 根据pid搜索 返回联动列表
     *
     * @param integer $pId 父id
     * @return array
     */
    public static function selectCascaderByPid(int $pId): array
    {
        if(!is_numeric($pId) || $pId < 0) {
            return [];
        }

        $list = ArticleCategoryModel::selectCascaderByPid($pId);

        return array_map(function($item){
            $item['leaf'] = $item['leaf'] === 1;
            return $item;
        },$list);
    }

    
}