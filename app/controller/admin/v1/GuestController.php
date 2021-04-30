<?php
// +----------------------------------------------------------------------
// | LqsBlog - API-Admin 来宾控制器
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\controller\admin\v1;

use jwt\CaptchaToken;
use utils\BodyResult;
use think\Response;

class GuestController
{

  /**
   * 图片验证码
   */
  public function validateCodeImg(): Response {
      return BodyResult::success(CaptchaToken::createCaptcha());
  }

}