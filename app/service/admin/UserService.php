<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin 系统用户 Service
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\service\admin;

use app\model\RoleResourceModel;
use enum\ResultCode;
use app\validate\User;
use app\model\UserModel;
use app\model\UserRoleModel;
use think\facade\Db;
use utils\Md5Hash;

class UserService
{
 

    /**
     * 用户登录
     *
     * @param array $param['username'=>'用户名', 'password'=> '密码']
     * @return array
     * @throws ValidateException
     */
    public static function loginUser(array $param = ['username'=>'', 'password'=> '']): array {

        // 验证参数，不通过会抛出异常
        validate(User::class)->scene('admin_service_login_user')->check($param);

        $user = UserModel::where('username', $param['username'])->find();
        if (!$user) {
            throw new \think\exception\ValidateException('用户名不存在');
        }

        $passwordMd5 = self::passWordSimpleHash($param['password'], $user->salt);
        if ($passwordMd5 !== $user->password) {
            throw new \think\exception\ValidateException('密码错误');
        }

        
        return [
            'id' => $user->id,
            'username' =>$user-> username,
            'nickname' => $user->nickname,
            'locked' => $user->locked,
        ];

    }

    /**
     * 返回用户信息包括角色权限等
     * 
     * @param array $user['id'=>'用户id', 'nickname'=> '昵称']
     * @return array
     * @throws ValidateException
     */
    public static function userInfo(array $user = ['id'=>0, 'nickname' => '']): array {

        if(!is_numeric($user['id']) || $user['id'] < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }
        
        $roleIds = [];
        $roles = [];
        $userRole = UserRoleModel::with(['role'])->where(['user_id' => $user['id']])->select();
        foreach($userRole as $item){
            $roleIds[] = $item->role_id;
            $roles[] = $item->role->name;
        }

        $resources = [];
        if(!empty($roleIds)) {
            $roleResource = RoleResourceModel::with(['resource'])->where('role_id', 'in', $roleIds)->select();
            foreach($roleResource as $item){
                $resources[] = $item->resource->urlcode;
            }
        }

        return [
            'name'=> $user['nickname'],
            'avatar'=> '',
            'resources'=> $resources,
            'roles'=> $roles,
            'msgtotal'=> 0,
        ];
    }

    /**
     * 随机盐
     *
     * @return string
     */
    public static function saltRandom(): string {
        return Md5Hash::saltRandom(8);
    }

    /**
     * 密码二进制md5加密3次
     *
     * @param string $password 密码
     * @param string $salt 盐
     * @return string
     */
    public static function passWordSimpleHash(string $password = '', string $salt = '' ): string {
        return Md5Hash::simpleHash($password, $salt, 3);
    }


    /**
     * 获取排序字段
     *
     * @param integer $i 下标
     * @return string
     */
    public static function getSort(int $i): string 
    {
        $sort = [ 'id' ];
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
     * 获取用户分页信息
     * 
     * @param array $paginate=[ 'per'=> 分页数, 'page'=>页码 ] 
     * @param array $search= [ 'keywords'=> '', 'sort'=> 排序字段下标[id,hit,addtime], 'order'=> 下标[desc 降序，asc 升序]] 搜索字段
     * @return array
     */
    public static function userPage(array $paginate=['per'=>10, 'page'=>1 ], array $search= [ 'keywords'=> '',  'sort'=> 0, 'order'=> 0]): array
    {

        // 默认分页参数
        $paginate['per'] = ($paginate['per']<1 || !is_numeric($paginate['per'])) ? 10 : intval($paginate['per']);
        $paginate['page'] = ($paginate['page']<1 || !is_numeric($paginate['page'])) ? 1 : intval($paginate['page']);        
       
        // 默认 search 字段
        $search['sort'] = (empty($search['sort']) || !is_numeric($search['sort'])) ? 0 : intval($search['sort']);
        $search['order'] = (empty($search['order']) || !is_numeric($search['order'])) ? 0 : intval($search['order']);
        

        // 搜索条件
        $whereOr = [];
        if(!empty($search['keywords'])) {
            $whereOr[] = ['username', 'like', '%' . $search['keywords'] . '%'];
            $whereOr[] = ['nickname', 'like', '%' . $search['keywords'] . '%'];
        }

        $total = UserModel::whereOr($whereOr)->count();
        $rows = UserModel::whereOr($whereOr)->order(self::getSort($search['sort']),  self::getOrderType($search['order']))->page($paginate['page'], $paginate['per'])->select();        
        $response = [
            'total' => $total,
            'currentPage' => $paginate['page'],
            'list' => []
        ];
        if($rows->isEmpty()) {
            return $response;
        }

        // 取出user_id
        $userIds = [];
        foreach($rows as $item){
            $userIds[] = $item->id;
        }

        // 设置用户对应角色
        $userRoleMap = [];
        $userRoleList = UserRoleModel::with(['role'])->where('user_id', 'in', $userIds)->select();        
        foreach ($userRoleList as $item) {
            $userId = $item->userId;
            $listvo = empty($userRoleMap[$userId]) ? [] : $userRoleMap[$userId];
            $listvo[] = [
                'id' => $item->roleId,
                'name' => empty($item->role->name)?'':$item->role->name,
            ];
            $userRoleMap[$userId] = $listvo;
        }

        // 设置返回数据列表
        $list =[];
        foreach($rows as $item){
            $list[] = [
                'id' => $item->id,
                'username' => $item->username,
                'nickname' => $item->nickname,
                'locked' => $item->locked,
                'roles' => empty($userRoleMap[$item->id]) ? [] : $userRoleMap[$item->id],
            ];
        }

        $response['list'] = $list;
        return $response; 
    }


    /**
     * 插入一条记录
     *
     * @param array $info 对应字段
     * @return array
     * @throws ValidateException
     */
    public static function save(array $info = ['username'=> '', 'password'=> '', 'nickname'=> '', 'roles'=> []]): array
    {
        // 验证参数，不通过会抛出异常
        validate(User::class)->scene('admin_service_save')->check($info);

        $salt = self::saltRandom();
        $passwordMD5 = self::passWordSimpleHash($info['password'], $salt);

        // 创建事务
        Db::startTrans();
        $user = [];
        try {

            $data = [
                'username' => $info['username'],            
                'password'=> $passwordMD5,
                'salt'=> $salt,
                'nickname'=> empty($info['nickname'])?'':$info['nickname'],
            ];
            $record = UserModel::create($data);
            $user = $record->toArray();

            // 修改角色资源关联表
            self::saveBatchUserRole(intval($record->id), $info['roles']);    

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {

            // 回滚事务
            Db::rollback();
            throw new \think\exception\ValidateException($e->getMessage());
        }       


        return $user;
    }

    /**
     * 根据 ID 修改
     *
     * @param array $info 对应分类字段
     * @return array
     * @throws ValidateException
     */
    public static function updateById(array $info=['id'=> 0, 'username'=> '', 'password'=> '', 'nickname'=> '', 'roles'=> []]): bool
    {
        // 验证参数，不通过会抛出异常
        $salt = '';
        $passwordMD5 = '';
        if(empty($info['password'])){
            validate(User::class)->scene('admin_service_update_nopwd')->check($info);
        } else {
            validate(User::class)->scene('admin_service_update')->check($info);
            $salt = self::saltRandom();
            $passwordMD5 = self::passWordSimpleHash($info['password'], $salt);
        }

        // 创建事务
        Db::startTrans();
        try {

            $data = [
                'username' => $info['username'],            
                'nickname'=> empty($info['nickname'])?'':$info['nickname'],
            ];
            if(!empty($salt) && !empty($passwordMD5)) {
                $data['salt'] = $salt;
                $data['password'] = $passwordMD5;
            }
            $record = UserModel::find($info['id']);
            $record->save($data);

            // 修改角色资源关联表
            self::saveBatchUserRole(intval($info['id']), $info['roles']);    
    
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {

            // 回滚事务
            Db::rollback();
            throw new \think\exception\ValidateException($e->getMessage());
        }       


        return true;
    }


    /**
     * 批量修改用户角色关联表
     *
     * @param integer $userId 用户id
     * @param array $roles 角色id数组
     * @return array
     * @throws ValidateException
     */
    public static function saveBatchUserRole(int $userId, $roles = []): array
    {
        if(!is_numeric($userId) || $userId < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        if(empty($roles)) {
            return [];
        }

        // 先清空同用户ID下 user role数据
        UserRoleModel::destroy(function($query) use($userId) {
            $query->where('user_id', $userId);
        });

        // 再批量添加
        $urlist = [];
        foreach ($roles as $value) {
            $urlist[] = [
                'user_id' => $userId,
                'role_id'=> $value,
            ];
        }

        $userRole = new UserRoleModel;
        return $userRole->saveAll($urlist)->toArray();

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

        // 创建事务
        Db::startTrans();
        try {

            UserModel::destroy(function($query) use($id) {
                $query->where('id', $id);
            });

            UserRoleModel::destroy(function($query) use($id) {
                $query->where('user_id', $id);
            });

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {

            // 回滚事务
            Db::rollback();
            throw new \think\exception\ValidateException($e->getMessage());
        }       
     
        return true;
    }


    /**
     * 根据 ID 获取 详情
     *
     * @param integer $id
     * @return array
     * @throws ValidateException
     */
    public static function getUserById(int $id): array{

        if(!is_numeric($id) || $id < 1) {
            throw new \think\exception\ValidateException(ResultCode::INCORRECT_PARAMETER['msg']);
        }

        $user = UserModel::find($id);
        if(!$user) {
            throw new \think\exception\ValidateException(ResultCode::NOT_FOUND['msg']);
        }

        $userRoleList = UserRoleModel::with(['role'])->where('user_id', $user->id)->select();   
        $roles = [];
        foreach ($userRoleList as $item) {
            $roles[] = [
                'id' => $item->roleId,
                'name' => empty($item->role->name)?'':$item->role->name,
            ];
        }
        
        return [
            'id'=> $user->id,
            'locked'=> $user->locked,
            'nickname'=> $user->nickname,
            'username'=> $user->username,
            'roles'=> $roles,
        ];
    }
    

}

