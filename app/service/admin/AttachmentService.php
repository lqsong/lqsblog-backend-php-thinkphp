<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 文件附件 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\AttachmentModel;
use enum\ResultCode;
use utils\Upload;

class AttachmentService 
{

    /**
     * 获取图片列表分页信息
     * 
     * @param array $paginate=[ 'per'=> 分页数, 'page'=>页码 ] 
     * @return array
     */
    public static function listPage(array $paginate=['per'=>10, 'page'=>1 ]) : array
    {

        // 默认分页参数
        $paginate['per'] = ($paginate['per']<1 || !is_numeric($paginate['per'])) ? 10 : intval($paginate['per']);
        $paginate['page'] = ($paginate['page']<1 || !is_numeric($paginate['page'])) ? 1 : intval($paginate['page']);        

        // 搜索条件
        $where = [];
        $where[] = ['file_type', 'in', ['image/png', 'image/gif', 'image/jpg', 'image/jpeg']];

        $total = AttachmentModel::where($where)->count();
        $attachment = AttachmentModel::where($where)->order('id',  'desc')->page($paginate['page'], $paginate['per'])->select(); 
        $response = [
            'total' => $total,
            'currentPage' => $paginate['page'],
            'list' => []
        ];
        if($attachment->isEmpty()) {
            return $response;
        }

        // 设置返回数据列表
        $list = [];
        foreach($attachment as $item){
            $list[] = [
                'id' => $item->id,
                'imgurl' => Upload::webUrl() .  $item->file_sub_dir . '/' . $item->file_name,
                'size' => round($item->file_size / 1024, 2) . 'KB',
            ];
        }

        $response['list'] = $list;
        return $response; 

    }

    /**
     * 图片上传创建
     *
     * @param string $name 表单控件名称
     * @param integer $creatorId 当前用户id
     * @return array
     * @throws ValidateException
     */
    public static function imgSave(string $name = 'file', int $creatorId): array
    {
        $file = Upload::putSingleImage($name);

        $data = [
            'file_old_name'=> $file['originalFileName'],
            'file_name'=> $file['fileName'],
            'file_sub_dir'=> $file['subDir'],
            'file_type'=> $file['type'],
            'file_suffix'=> $file['suffix'],
            'file_size'=> $file['size'],
            'creator_id'=> $creatorId,
            'create_time'=> date("Y-m-d H:i:s"),
        ];

        AttachmentModel::create($data);

        return [
            'title' => $file['fileName'],
            'url' => Upload::webUrl() .  $file['subDir'] . '/' . $file['fileName'],
        ];
    }

    /**
     * 根据 ID 获取详情
     *
     * @param integer $id id
     * @return array
     * @throws ValidateException
     */
    public static function getImgById(int $id): array
    {
        if(!is_numeric($id) || $id < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        $attachment = AttachmentModel::find($id);
        if(!$attachment) {
            throw new \think\exception\ValidateException(ResultCode::NOT_FOUND['msg']);
        }

        return [
            'fileName' => $attachment->file_name,
            'filePath' => Upload::getUploadDirLocation() .   $attachment->file_sub_dir . DIRECTORY_SEPARATOR . $attachment->file_name,
        ];
    }


    


}