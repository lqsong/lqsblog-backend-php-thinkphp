<?php
// +----------------------------------------------------------------------
// | LqsBlog - jwt - token
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace jwt;

use Firebase\JWT\JWT;
use think\facade\Config;

class Token 
{

    /**
     * 设置认证token
     *
     * @param string|array|number $data 
     * @return string
     */
    public static function createJWT($data): string
    {

        $time = time();
        $token = [
        	// 'iss' => 'http://liqingsong.cc', //签发者 可选
           	// 'aud' => 'http://liqingsong.cc', //接收该JWT的一方，可选
           	'iat' => $time, // 签发时间
           	'nbf' => $time , // (Not Before)：某个时间点后才能访问，比如设置 time + 30，表示当前时间30秒后才能使用
           	'exp' => $time + Config::get('jwt.expires_in'), // 过期时间
            'data' => $data
        ];

        return JWT::encode($token, Config::get('jwt.secret'), 'HS256');

    }

    /**
     * 解析token字符串
     *
     * @param string $token
     * @return array|null
     */
    public static function parseJWT(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, Config::get('jwt.secret'), ['HS256']);
            return (array)$decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 根据过期时间与重置时间，重置认证token
     *
     * @param string $token
     * @param string|array|number $data 
     * @return string|null
     */
    public static function restJwt(string $token, $data): ?string
    {
        $bool = self::expirationNewJWT($token);
        if ($bool) {
            return self::createJWT($data);
        }
        return null;
    }


    /**
     * 解析token字符串, 判断失效时间，返回是否生成新的token
     *
     * @param string $token
     * @return boolean
     */
    public static function expirationNewJWT(string $token) : bool
    {
        $decoded = self::parseJWT($token);
        if ($decoded !== null) {
            $exp = $decoded['exp'];
            $time = time();
            if ($exp - $time <= Config::get('jwt.rest_expires_in')) {
                return true;
            }
        }
        return false;
    }
    
}