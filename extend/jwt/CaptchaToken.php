<?php
// +----------------------------------------------------------------------
// | LqsBlog - jwt - CaptchaToken
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace jwt;

use Gregwar\Captcha\PhraseBuilder;
use Gregwar\Captcha\CaptchaBuilder;
use jwt\Token;
use Exception;

class CaptchaToken
{

    /**
     * 获取 base64与code
     *
     * @param integer $len 随机个数
     * @param integer $width 宽
     * @param integer $height 高
     * @return array
     */
    public static function getCaptchaBase64Code(int $len = 4, int $width = 160, int $height = 40): array
    {
        $phraseBuilder = new PhraseBuilder($len);
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $captcha = $builder->build($width, $height);

        return [
            'code' => $captcha->getPhrase(),
            'base64' => $captcha->inline()
        ];
    }

    /**
     * 生成 token 图片验证码
     *
     * @param integer $len 随机个数
     * @param integer $width 宽
     * @param integer $height 高
     * @return array
     */
    public static function createCaptcha(int $len = 4, int $width = 160, int $height = 40): array
    {
        $captcha = self::getCaptchaBase64Code($len, $width, $height);
        return [
            'tokenCode' =>  Token::createJWT($captcha['code']),
            'base64' => $captcha['base64']
        ];
    }


    /**
     * 解析token图片验证码 返回 验证码字符串
     *
     * @param string $token 加密的验证码字符串
     * @return mixed
     */
    public static function parseCaptcha(string $token)
    {
        $decoded = Token::parseJWT($token);
        if ($decoded === null) {
            return null;
        }
        return $decoded['data'];
    }

    /**
     * 验证token图片验证码,不区分大小写
     *
     * @param string $token
     * @param string $code
     * @return boolean
     */
    public static function verifyCaptcha(string $token, string $code): bool
    {
        try {
            $decode = self::parseCaptcha($token);
            $decode = $decode ? strtolower($decode) : '';
            return $decode === strtolower($code);
        } catch (Exception $e) {
           return false;
        }
    }


}