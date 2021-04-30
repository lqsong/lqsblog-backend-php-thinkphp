<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 站点配置 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\ConfigModel;
use enum\ResultCode;

class ConfigService 
{

    /**
     * 获取配置
     *
     * @return array
     */
    public static function getAll(): array
    {
        $list = ConfigModel::select();
        $response =[];
        foreach ($list as $item) {
            $response[$item->name] = $item->content;
        }
        return $response;
    }

    /**
     * 修改配置
     *
     * @param array $feild 键值对 {'name':'conent'}
     * @return boolean
     * @throws ValidateException
     */
    public static function updateAll(array $feild = ['name'=>'conent']):bool
    {
        if(empty($feild)) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        foreach ($feild as $key => $value) {
            $record = ConfigModel::where('name', $key)->find();
            $record->save(['content'=> $value]);
        }

        return true;
    }



}