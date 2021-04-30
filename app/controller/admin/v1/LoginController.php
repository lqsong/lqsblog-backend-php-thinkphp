<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 登录控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\admin\v1;

use think\Request;
use think\exception\ValidateException;
use think\Response;
use app\validate\User;
use app\service\admin\UserService;
use jwt\CaptchaToken;
use jwt\Token;
use utils\BodyResult;

class LoginController  
{

    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 构造方法
     * @access public
     * @param Request $request Request对象
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

    }

    /**
     * 登录
     */
    public function index(): Response {

        $username = $this->request->post('username');
        $password = $this->request->post('password');
        $imgCode = $this->request->post('imgCode');
        $imgCodeToken = $this->request->post('imgCodeToken');

        try {
            validate(User::class)->check([
                'username' => $username,
                'password' => $password,
                'imgCode' => $imgCode,
                'imgCodeToken' => $imgCodeToken,
            ]);
        } catch (ValidateException $e) {
            return BodyResult::validate($e->getError());
        }

        if(!CaptchaToken::verifyCaptcha($imgCodeToken, $imgCode)) {
            return BodyResult::validate('验证码不正确');
        }

        try {
            $user = UserService::loginUser(['username'=> $username,'password'=>$password]);
            $token = Token::createJWT($user);
            return BodyResult::success(['token'=> $token]);
        } catch (ValidateException $e) {
            return BodyResult::error('用户名或密码错误');
        }


    }
}
