<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 专题 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\TagModel;
use app\validate\Tag;
use enum\ResultCode;

class TagService 
{

    /**
     * 获取排序字段
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getSort(int $i): string 
    {
        $sort = [ 'id', 'hit'];
        $len = count($sort);
        return ($i > $len || $i < 0) ? $sort[0] : $sort[$i];
    }

    /**
     * 获取排序类型
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getOrderType(int $i): string
    {
        return $i === 0 ? 'desc' : 'asc';
    }


    /**
     * 获取分页信息
     * 
     * @param array $paginate=[ 'per'=> 分页数, 'page'=>页码 ] 
     * @param array $search= [ 'keywords'=> '', 'sort'=> 排序字段下标[id,hit,addtime], 'order'=> 下标[desc 降序，asc 升序]] 搜索字段
     * @return array
     */
    public static function listPage(array $paginate=['per'=>10, 'page'=>1 ], array $search= ['keywords'=> '', 'sort'=> 0, 'order'=> 0]) : array
    {

        // 默认分页参数
        $paginate['per'] = ($paginate['per']<1 || !is_numeric($paginate['per'])) ? 10 : intval($paginate['per']);
        $paginate['page'] = ($paginate['page']<1 || !is_numeric($paginate['page'])) ? 1 : intval($paginate['page']);        
       
        // 默认 search 字段
        $search['sort'] = (empty($search['sort']) || !is_numeric($search['sort'])) ? 0 : intval($search['sort']);
        $search['order'] = (empty($search['order']) || !is_numeric($search['order'])) ? 0 : intval($search['order']);
        
        

        // 搜索条件
        $where = [];
        if(!empty($search['keywords'])) {
            $where[] = ['title', 'like', '%' . $search['keywords'] . '%'];
        }


        $total = TagModel::where($where)->count();
        $works = TagModel::where($where)->order(self::getSort($search['sort']),  self::getOrderType($search['order']))->page($paginate['page'], $paginate['per'])->select();        
        return [
            'total' => $total,
            'currentPage' => $paginate['page'],
            'list' => $works->hidden(['content'])->toArray()
        ];

    }


    /**
     * 插入一条记录
     *
     * @param array $info 对应字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info=['name'=> '', 'pinyin'=> '',  'hit'=> '']): array 
    {
        
        // 验证参数，不通过会抛出异常
        validate(Tag::class)->scene('admin_service_save')->check($info);

        $count = TagModel::where('name', $info['name'])->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('存在相同名称');
        }

        $data = [
            'name'=> $info['name'],
            'pinyin'=> $info['pinyin'],
            'hit'=> empty($info['hit'])?0:$info['hit'],
        ];
        $record = TagModel::create($data);

        return $record->toArray();
    }

    /**
     * 根据 ID 修改
     *
     * @param array $info 对应字段
     * @return boolean
     * @throws ValidateException
     */
    public static function updateById(array $info=['id'=>0, 'name'=> '', 'pinyin'=> '',  'hit'=> '']): bool
    {
        // 验证参数，不通过会抛出异常
        validate(Tag::class)->scene('admin_service_update')->check($info);

        $count = TagModel::where([
            ['name', '=', $info['name']],
            ['id', '<>', $info['id']]
        ])->count();
        if($count > 0) {
            throw new \think\exception\ValidateException('存在相同名称');
        }

        $data = [
            'name'=> $info['name'],
            'pinyin'=> $info['pinyin'],
        ];
        if(!empty($info['hit'])) {
            $data['hit'] = $info['hit'];
        }

        $record = TagModel::find($info['id']);
        return $record->save($data);


    }


    /**
     * 根据 ID 删除
     *
     * @param integer $id id
     * @return boolean
     * @throws ValidateException
     */
    public static function removeById(int $id): bool
    {
        if(!is_numeric($id) || $id < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        return TagModel::destroy(function($query) use($id) {
            $query->where('id', $id);
        });
    }


    /**
     * 根据搜索返回列表
     *
     * @param string $keywords 搜索关键词
     * @return array
     */
    public static function searchKeywordsLimit(string $keywords): array
    {
        // 搜索条件
        $where = [];
        if(!empty($keywords)) {
            $where[] = ['name', 'like', '%' . $keywords . '%'];
            $where[] = ['pinyin', 'like', '%' . $keywords . '%'];
        }

         return TagModel::whereOr($where)->limit(10)->select()->toArray();
    }


}