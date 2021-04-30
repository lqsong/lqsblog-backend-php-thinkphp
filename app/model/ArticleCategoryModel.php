<?php
// +----------------------------------------------------------------------
// | LqsBlog - 文章分类 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class ArticleCategoryModel extends Model
{

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    // 模型名
    protected $name = 'article_category';

    // 设置字段信息
    protected $schema = [
        'id'              => 'int',
        'p_id'            => 'int',
        'name'            => 'string',
        'alias'           => 'string',
        'title'           => 'string',
        'keywords'        => 'string',
        'description'     => 'string',
        'hit'             => 'int',
    ];


    /**
     * 自定义查询Cascader
     *
     * @param integer $pId 父id
     * @return array
     */
    public static function selectCascaderByPid(int $pId): array
    {
        $model = new static();
        // $query = $model->db();

        // SELECT COUNT(`b`.`id`) AS think_count FROM `lqs_article_category` `b` WHERE  `b`.`p_id` = `a`.`id`
        $subQuery = $model->alias('b')->whereRaw('`b`.`p_id` = `a`.`id`')->fetchSql(true)->count('b.id');
        // SELECT a.id, a.name,( CASE WHEN (SELECT COUNT(`b`.`id`) AS think_count FROM `lqs_article_category` `b` WHERE  `b`.`p_id` = `a`.`id`)>0 THEN false ELSE true END) as leaf FROM `lqs_article_category` `a` WHERE  `a`.`p_id` = '0'
        $result = $model->alias('a')->fieldRaw('a.id, a.name,( CASE WHEN ('. $subQuery .')>0 THEN false ELSE true END) as leaf')->where('a.p_id', $pId)->select();
        return $result->toArray();
    }



}

