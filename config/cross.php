<?php
// +----------------------------------------------------------------------
// | 跨域配置
// +----------------------------------------------------------------------

return [
    // 允许访问域名(允许所有域名访问 header('Access-Control-Allow-Origin: *');允许单个域名访问header('Access-Control-Allow-Origin: https://test.com');)
    'allow_origin'      => env('cross.allow_origin', '*'),
    // 响应类型
    'allow_methods'          => env('cross.allow_methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS'),
    // 带 cookie 的跨域访问
    'allow_credentials'      => env('cross.allow_credentials', 'false'),
    // 响应头设置
    'allow_headers'          => env('cross.allow_headers', ''),
    // 缓存时间
    'max_age'          => env('cross.max_age', 1800),    
];
