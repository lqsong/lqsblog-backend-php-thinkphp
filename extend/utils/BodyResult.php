<?php
// +----------------------------------------------------------------------
// | LqsBlog - body result Utils
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace utils;

use enum\ResultCode;
use think\Response;

class BodyResult {


    /**
     * 处理成功响应
     *
     * @param array $data
     * @param string $token
     * @param string $msg
     * @param integer $code
     * @return Response
     */
    public static function success(array $data = [], string $token = '', string $msg = ResultCode::SUCCESS['msg'], int $code = ResultCode::SUCCESS['code']): Response {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        if($token!=='') {
            $return['token'] = $token;
        }
        return json($return);
    }


    /**
     * 处理错误响应
     *
     * @param string $msg
     * @param integer $code
     * @param array $data
     * @return Response
     */
    public static function error(string $msg = ResultCode::FAIL['msg'], int $code = ResultCode::FAIL['code'], array $data = []): Response {
        $return = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];
        return json($return);
    }

    /**
     * 验证错误响应
     *
     * @param string|array $msg
     * @param integer $code
     * @return Response
     */
    public static function validate($msg, int $code = ResultCode::VERIFICATION_FAILED['code']): Response {
        $return = [
            'code' => $code,
            'msg'  => $msg,
        ];
        if(is_array($msg)) {
            $return['msg'] = ResultCode::VERIFICATION_FAILED['msg'];
            $return['errors'] = $msg;
        }
        return json($return);
    }


}
