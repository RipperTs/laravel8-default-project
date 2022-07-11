<?php


namespace App\Http\Tools;

use tinymeng\code\Generate as GenerateCode;

/**
 * 二维码生成
 * Class Qrcode
 * @package App\Http\Tools
 * Author Ripper. 2022/4/2
 */
class Qrcode
{

    /**
     * 生成二维码
     * @param $text -生成内容
     * @param string $path -二维码路径
     * @return array
     */
    public static function generate($text, $path = '/temp/qrcode')
    {
        $path = '/' . trim($path, '/') . '/';
        $generate = GenerateCode::qr();
        $dirPath = public_path() . $path;
        !is_dir($dirPath) && mkdir($dirPath, 0777, true);
        $file_path = $generate->create($text, $dirPath);
        return [
            'file_path' => $file_path,
            'public_url' => getenv('APP_URL') . $path . pathinfo($file_path)['basename']
        ];
    }

}
