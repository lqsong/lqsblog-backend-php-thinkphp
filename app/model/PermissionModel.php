<?php
// +----------------------------------------------------------------------
// | LqsBlog - 系统api服务权限 Model
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\model;

use think\Model;

class PermissionModel extends Model
{
    // 模型名
    protected $name = 'sys_permission';

    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'permission'  => 'string',
        'description' => 'string',
        'pid'         => 'int',
    ];

    /**
     * 自定义查询Cascader
     *
     * @param integer $pid 父id
     * @return array
     */
    public static function selectCascaderByPid(int $pid): array
    {
        $model = new static();
        // $query = $model->db();

        // SELECT COUNT(`b`.`id`) AS think_count FROM `lqs_sys_permission` `b` WHERE  `b`.`pid` = `a`.`id`
        $subQuery = $model->alias('b')->whereRaw('`b`.`pid` = `a`.`id`')->fetchSql(true)->count('b.id');
        // SELECT a.id, a.name,( CASE WHEN (SELECT COUNT(`b`.`id`) AS think_count FROM `lqs_sys_permission` `b` WHERE  `b`.`pid` = `a`.`id`)>0 THEN false ELSE true END) as leaf FROM `lqs_sys_permission` `a` WHERE  `a`.`pid` = '0'
        $result = $model->alias('a')->fieldRaw('a.id, a.name,( CASE WHEN ('. $subQuery .')>0 THEN false ELSE true END) as leaf')->where('a.pid', $pid)->select();
        return $result->toArray();
    }

}

