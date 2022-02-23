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
     * @param      $file
     * @param      $folder
     * @param      $filePrefix
     * @param bool $maxWidth
     *
     * @return false|string[]
     */
    public function store($file, $folder, $filePrefix, $maxWidth = false)
    {
        // 构建存储文件夹规则
        $folderName = "uploads/images/$folder/" . date("Ym/d", time());

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

        // 如果限制了图片宽度，就进行裁剪
        if ($maxWidth && $extension != 'gif') {
            $this->reduceSize($uploadDiskPath . '/' . $fileName, $maxWidth);
        }

        return [
            'path' => config('app.url') . "/$folderName/$fileName",
        ];
    }

    public function reduceSize($fileDiskPath, $maxWidth)
    {
        // 实例化图片
        $image = \Image::make($fileDiskPath);

        // 大小调整
        $image->resize($maxWidth, null, function($constraint) {
            // 设定宽度是 $max_width，高度等比例缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }
}
