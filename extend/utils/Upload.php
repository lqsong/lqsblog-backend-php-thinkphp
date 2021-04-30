<?php
// +----------------------------------------------------------------------
// | LqsBlog - Upload Utils
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace utils;

use think\facade\Config;

class Upload {


    protected static $disk = 'lqsblog';


    /**
     * 获取上传配置
     *
     * @return array
     */
    public static function getConfig(): array
    {
        return Config::get('filesystem.disks.' . self::$disk);
    }

    /**
     * 获取网站upload网址
     *
     * @return string
     */
    public static function webUrl(): string
    {
       $config = self::getConfig();
       return $config['url'] . '/'; 
    }

    /**
     * 获取文件在本地存储的绝对地址
     *
     * @return string
     */
    public static function getUploadDirLocation(): string
    {
        $config = self::getConfig();
        return $config['root'] . DIRECTORY_SEPARATOR; 
     }


    /**
     * 上传单张图片
     *
     * @param string $name 表单控件名称
     * @return array
     * @throws ValidateException
     */
    public static function putSingleImage(string $name = 'file'): array
    {
        $config = self::getConfig();
        $file = request()->file($name);
        // 获取文件类型
        $type = $file->getMime();
        if(!in_array($type, $config['img_type'])) {
            throw new \think\exception\ValidateException('文件上传类型要求：' .implode(',', $config['img_type']));
        }

        // 获取文件大小字节
        $size = $file->getSize();
        if($size > $config['img_size']) {
            throw new \think\exception\ValidateException('文件上传大小要求：' . round($config['img_size']/1048576, 2) . 'M');
        }
        
        // 获取文件后缀名
        $suffix = '.' . $file->extension();
        // 获取原文件名
        $originalFileName = $file->getOriginalName();
        // 自定义子目录        
        $subDir = date('Ymd');
        // 自定义新文件名
        $newFileName = md5((string) microtime(true)). $suffix;

        // 上传
        $savename = \think\facade\Filesystem::disk(self::$disk)->putFileAs('', $file, $subDir. DIRECTORY_SEPARATOR . $newFileName);

        return [
            'type' => $type,
            'suffix' => $suffix,
            'size' => $size,
            'originalFileName' => $originalFileName,
            'subDir' => $subDir,
            'fileName' => $newFileName,
            'savename' =>  $savename
        ];
    }


    

}