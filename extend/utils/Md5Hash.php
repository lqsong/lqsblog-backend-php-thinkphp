<?php
// +----------------------------------------------------------------------
// | LqsBlog - md5 hash Utils
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace utils;

use think\helper\Str;

class Md5Hash {

    /**
     * 随机盐
     *
     * @param integer $len 长度 返回长度len
     * @return string
     */
    public static function saltRandom(int $len = 8): string {
        return Str::random($len);
    }


    /**
     * 密码二进制md5加密{hashIterations}次
     *
     * @param string $source 加密的数据
     * @param string $salt 盐
     * @param integer $hashIterations 加密次数 默认3
     * @return string
     */
    public static function simpleHash(string $source = '', string $salt = '', int $hashIterations = 3): string {

        $iterations = $hashIterations<1 ? 0: $hashIterations-1;
        $hashed = $salt.$source;        
        for ($index = 0; $index < $iterations; $index++) {
            $hashed = md5($hashed, true); // 二进制加密
        }

        return md5($hashed);
    }

   












}