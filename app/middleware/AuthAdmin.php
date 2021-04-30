<?php
// +----------------------------------------------------------------------
// | LqsBlog - Admin Auth(验证权限) Middleware
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------

declare (strict_types = 1);

namespace app\middleware;

use think\exception\ValidateException;
use app\service\admin\PermissionService;
use enum\ResultCode;
use utils\BodyResult;

class AuthAdmin
{

    public function handle($request, \Closure $next, string $permissionName = '', string $permissionOper)
    {
        // 获取控制器的传参(当前用户信息)
        $currentUser = $request->middleware('currentUser');
        $permission = $permissionName . ':' . $permissionOper;

        try {
            $perms = PermissionService::listPermissionByUserId($currentUser['id']);
            if(in_array($permission, $perms)) {                
                return $next($request);
            } else {
                return BodyResult::error(ResultCode::UNAUTHORISE['msg']);
            }            
        } catch (ValidateException $e) {
            return BodyResult::error($e->getError());
        }  
        
    }
}