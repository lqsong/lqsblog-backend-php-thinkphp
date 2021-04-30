<?php

return [
    // 默认磁盘
    'default' => env('filesystem.driver', 'lqsblog'),
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/storage',
            // 磁盘路径对应的外部URL路径
            'url'        => '/storage',
            // 可见性
            'visibility' => 'public',
        ],
        // 更多的磁盘配置信息
        'lqsblog' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => env('filesystem.upload_dir', app()->getRootPath() . 'public/uploads'),
            // 磁盘路径对应的外部URL路径
            'url'        => env('filesystem.upload_weburl', '/uploads'),
            // 图片上传大小 1M(1(M)*1024(KB)*1024(B))
            'img_size'   => 1048576, 
            // 图片上传类型
            'img_type'   => [ 'image/png', 'image/gif', 'image/jpg', 'image/jpeg' ],
            // 可见性
            'visibility' => 'public',            
        ],
    ],
];
