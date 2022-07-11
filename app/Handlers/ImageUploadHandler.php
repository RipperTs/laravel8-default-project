<?php


namespace App\Handlers;

use Illuminate\Support\Facades\Storage;
use Obs\ObsClient;
use Obs\ObsException;
class ImageUploadHandler
{
    // 只允许以下后缀名的图片文件上传
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg','mp4', 'mp3'];

    public function save($file, string $folder = 'home',string $source = 'huike')
    {
        if (!$file) {
            abort(404,'没有找到文件');
//            throw new \JsonSchema\Exception\ValidationException('没有找到文件');
        }

        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        // 如果上传的文件不在允许列表内则不允许上传
        if (!in_array($extension, $this->allowed_ext)) {
             abort(404,'不支持的文件类型');
        }
        if ($source=='cjk')
        {
            $name = 'oss_cjk';
            $objectname = getenv('CJK_OSS_OBJECTNAME');
            $oss_url = getenv('CJK_OSS_URL') ;
        }else {
            $name = 'oss_huike';
            $objectname = getenv('HUIKE_OSS_OBJECTNAME');
            $oss_url = getenv('HUIKE_OSS_URL') ;
        }

        if ($extension=='mp4')
        {
            $objectname = 'video';
        } elseif ('mp3' === $extension) {
            $objectname = 'audio';
        }

        $disk = Storage::disk($name);
        $path = $disk->put($objectname.'/' . $folder, $file);
        //$url = $disk->getUrl($path);
        $url = $oss_url .  $path;

        return [
            'url' => $url,
            'path' => $path
        ];
    }

    public function local_file_save($file, string $folder = 'home',string $source = 'huike')
    {

        if ($source=='cjk')
        {
            $name = 'oss_cjk';
            $objectname = getenv('CJK_OSS_OBJECTNAME');
            $oss_url = getenv('CJK_OSS_URL') ;
        }else {
            $name = 'oss_huike';
            $objectname = getenv('HUIKE_OSS_OBJECTNAME');
            $oss_url = getenv('HUIKE_OSS_URL') ;
        }

        $disk = Storage::disk($name);
        $path = $disk->put($objectname.'/' . $folder, $file);

        $url = $oss_url .  $path;

        return [
            'url' => $url,
            'path' => $path
        ];
    }


    public function obs($file,$path='public'){
        if ($file) {
            $dirname = Storage::disk()->getDriver()->getAdapter()->getPathPrefix();
            $avatar = $file->store('/public');
            $file_realpath=$dirname.$avatar;
            $info = pathinfo($file_realpath);
            $ext = $info['extension'];
            $key=$path.'/'.md5(microtime() . uniqid()).'.'.$ext;
            $obsClient = new ObsClient([
                   'key' => getenv('HUIKE_OBS_ACCESS_KEY'),
                   'secret' => getenv('HUIKE_OBS_SECRET_KEY'),
                   'endpoint' => getenv('HUIKE_OBS_ENDPOINT'),
            ]);
            $resp = $obsClient->putObject([
                   'Bucket' => getenv('HUIKE_OBS_BUCKET'),
                   'Key' => $key,
                   'SourceFile' => $file_realpath  // localfile为待上传的本地文件路径，需要指定到具体的文件名
            ]);
            if (isset($resp['ObjectURL']) && !empty($resp['ObjectURL'])) {
                // 不保留原图，删除图片
                @unlink($file_realpath);
                $url=$resp['ObjectURL'];
                if (getenv('HUIKE_OBS_PATH')) {
                    $url=str_replace(getenv('HUIKE_OBS_BUCKET').'.'. getenv('HUIKE_OBS_ENDPOINT').':443',getenv('HUIKE_OBS_PATH'),$url);
                }
                return [
                    'url' => $url,
                    'path' => $key,
                ];
            }else{
                  return abort(422, '上传失败！');
            }
        }
        return abort(422, '请选择图片！');
    }

}
