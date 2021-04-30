<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin BaseController
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------

declare (strict_types = 1);

namespace app\controller\admin;

use think\App;
use think\Response;
use think\facade\Config;
use think\exception\HttpResponseException;
use jwt\Token;
use enum\ResultCode;
use utils\BodyResult;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 当前登录用户信息
     * @var array
     */
    protected $currentUser = [];

    /**
     * 新的token
     * @var string|null
     */
    protected $newToken = null;

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        $token = $this->request->header(Config::get('jwt.header_token_key'));
        $token = $token ? $token: '';
        $decoded = Token::parseJWT($token);
        if($decoded === null) {
            throw new HttpResponseException(BodyResult::error(ResultCode::UNAUTHENTICATED['msg'],ResultCode::UNAUTHENTICATED['code']));
        }

        $this->currentUser = (array)$decoded['data'];
        if(empty($this->currentUser['id'])) {
            throw new HttpResponseException(BodyResult::error(ResultCode::UNAUTHENTICATED['msg'],ResultCode::UNAUTHENTICATED['code']));
        }

        $this->request->currentUser = $this->currentUser; // 传参给中间件
        $this->newToken = Token::restJwt($token,$this->currentUser);       

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 处理成功响应
     *
     * @param array $data
     * @param string $msg
     * @param integer $code
     * @return Response
     */
    protected function _success(array $data = [], string $msg = ResultCode::SUCCESS['msg'], int $code = ResultCode::SUCCESS['code']): Response 
    {
        return BodyResult::success($data, $this->newToken ? $this->newToken: '', $msg, $code);
    }

    /**
     * 处理错误响应
     *
     * @param string $msg
     * @param integer $code
     * @param array $data
     * @return Response
     */
    protected function _error(string $msg = ResultCode::FAIL['msg'], int $code = ResultCode::FAIL['code'], array $data = []): Response
    {
        return BodyResult::error($msg, $code, $data);
    }

    /**
     * 验证错误响应
     *
     * @param string|array $msg
     * @param integer $code
     * @return Response
     */
    protected function _validate($msg, int $code = ResultCode::VERIFICATION_FAILED['code']): Response
    {
        return BodyResult::validate($msg, $code);
    }  
    
    /**
     * 获取分页数量和当前页码
     *
     * @return array
     */
    protected function getPerPage(): array 
    {
        $per = $this->request->param('per');
        $page = $this->request->param('page');
        return [
            'per' => ($per<1 || !is_numeric($per)) ? 10 : $per,
            'page' => ($page<1 || !is_numeric($page)) ? 1 : $page
        ];
    }

}
