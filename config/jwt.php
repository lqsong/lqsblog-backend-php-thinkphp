<?php
// +----------------------------------------------------------------------
// | JWT Token配置
// +----------------------------------------------------------------------

return [
    // 签名私钥
    'secret'          => env('jwt.secret', 'lqsblog'),
    // 签名失效时间 - 秒 3600（1小时）
    'expires_in'      => env('jwt.expires_in', 3600),
    // 距离签名失效时间多少内可以重置签名- 秒 1800（0.5小时）
    'rest_expires_in' => env('jwt.expires_in', 1800),
    // Header头 Token 名称
    'header_token_key' => env('jwt.header_token_key', 'lqsblog-token'),
];
