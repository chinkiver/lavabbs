<?php

namespace App\Handlers;

use Illuminate\Support\Str;

class ImageUploadHandler
{
    // 只允许以下后缀名的图片文件上传
    protected $allowedExt = ["png", "jpg", "gif", 'jpeg'];

    /**
     * 上传文件
     *
     * @param $file
     * @param $folder
     * @param $filePrefix
     *
     * @return false|string[]
     */
    public function store($file, $folder, $filePrefix)
    {
        // 构建存储文件夹规则
        $folderName = "uploads/images/$folder" . date("Ym/d", time());

        // 物理存储地址
        $uploadDiskPath = public_path() . '/' . $folderName;

        // 获取文件后缀
        $extension = strtolower($file->getClientOriginalExtension()) ? : '.png';

        // 拼接文件名
        $fileName = $filePrefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;

        // 如果上传的不是图片将终止操作
        if (! in_array($extension, $this->allowedExt)) {
            return false;
        }

        // 移动到指定目录
        $file->move($uploadDiskPath, $fileName);

        return [
            'path' => config('app.url') . "/$folderName/$fileName",
        ];
    }
}
