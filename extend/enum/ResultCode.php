<?php
// +----------------------------------------------------------------------
// | LqsBlog - result code enum
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace enum;

class ResultCode {

    const NOT_FOUND = [
        'code' => 404,
        'msg' => '资源不存在'
    ];

    const SUCCESS = [
        'code' => 0,
        'msg' => '操作成功'
    ];

    // ---系统错误返回码-----
    const FAIL = [
        'code' => 10001,
        'msg' => '操作失败'
    ];

    const UNAUTHENTICATED = [
        'code' => 10002,
        'msg' => '当前用户登录信息无效,请重新登录!'
    ];

    const UNAUTHORISE = [
        'code' => 10003,
        'msg' => '权限不足'
    ];

    const ACCOUNT_LOCKOUT = [
        'code' => 10004,
        'msg' => '账号锁定'
    ];

    const INCORRECT_PARAMETER = [
        'code' => 10005,
        'msg' => '参数不正确'
    ];

    const VERIFICATION_FAILED = [
        'code' => 10006,
        'msg' => '验证不通过'
    ];

    // ---服务器错误返回码-----
    const SERVER_ERROR = [
        'code' => 99999,
        'msg' => '抱歉，系统繁忙，请稍后重试'
    ];



    public static function getConstants(): array {
        $oClass = new \ReflectionClass(__CLASS__);

        return $oClass->getConstants();
    }

}

