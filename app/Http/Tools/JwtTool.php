<?php
/**
 * Created by PhpStorm.
 * User: xinu x-php@outlook.com
 * Coding Standard: PSR2
 * Date: 2020-06-12
 * Time: 11:54
 */


namespace App\Http\Tools;
use Firebase\JWT\JWT;

class JwtTool
{
    protected static $key = '';

    /**
     * User: xinu
     * Time: 2020-06-12 13:45
     * Desc: 加密
     * Uri :
     * @param $payload
     * @param string $key
     * @param string $shaTyp
     * @return string
     * @throws \Exception
     */
    public static function encode($payload, $key = '',  $shaTyp = 'HS256')
    {
        self::$key = $key ? $key : config('app.key');
        if (empty(self::$key)) throw new \Exception('jwt app.key不可为空', 9901);
        return JWT::encode($payload, self::$key, $shaTyp);
    }

    /**
     * User: xinu
     * Time: 2020-06-12 13:45
     * Desc: 解密
     * Uri :
     * @param $jwt
     * @param string $key
     * @param string[] $shaTyp
     * @return array
     * @throws \Exception
     */
    public static function decode($jwt, $key ='', $shaTyp = ['HS256'])
    {
        self::$key = $key ? $key : config('app.key');
        if (empty(self::$key)) throw new \Exception('jwt app.key不可为空', 9901);
        return (array)JWT::decode($jwt, self::$key, $shaTyp);
    }
}
