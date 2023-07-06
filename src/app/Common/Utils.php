<?php

declare(strict_types=1);

namespace App\Common;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Exception;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Imagick;
use ImagickPixel;
use League\Flysystem\Filesystem;

/*
 * 助手类
 */

class Utils
{
    /**
     * 将pdf文件转化为多张png图片.
     *
     * @param string $pdf pdf所在路径 （/public/pdf/abc.pdf pdf所在的绝对路径）
     * @param string $path 新生成图片所在路径 (/public/pdf/)
     *
     * @throws Exception
     */
    public static function pdfToMultiPng(string $pdf, string $path): bool|array
    {
        if (!extension_loaded('imagick')) {
            return false;
        }

        if (!file_exists($pdf)) {
            return false;
        }
        $im = new Imagick();
        $im->setResolution(120, 120); //设置分辨率 值越大分辨率越高
        $im->setCompressionQuality(100);
        $im->readImage($pdf);
        $returnArr = [];

        foreach ($im as $k => $v) {
            $v->setImageFormat('png');
            $fileName = $path . md5($k . time()) . '.png';

            if ($v->writeImage($fileName) == true) {
                $returnArr[] = $fileName;
            }
        }
        return $returnArr;
    }

    /**
     * 将pdf转化为单一png图片
     * 注意使用该函数库首先需要安装imagick扩展及
     * 软件ImageMagic，yum install ImageMagick
     * 然后再安装 yum install -y ghostscript
     * php 通过 Imagic 扩展去调用ImageMagic,ImageMagic去调用 GhostScript 将pdf转换为 png,接着 ImageMagic对png进行处理.
     *
     * @param string $pdf pdf所在路径 （/public/pdf/abc.pdf pdf所在的绝对路径）
     * @param string $path 新生成图片所在路径 (/public/pdf/)
     *
     * @throws Exception
     */
    public static function pdfToOnePng(string $pdf, string $path): string
    {
        try {
            $im = new Imagick();
            $im->setCompressionQuality(100);
            $im->setResolution(120, 120); //设置分辨率 值越大分辨率越高
            $im->readImage($pdf);

            $canvas = new Imagick();
            $imgNum = $im->getNumberImages();
            //$canvas->setResolution(120, 120);
            foreach ($im as $k => $sub) {
                $sub->setImageFormat('png');
                //$sub->setResolution(120, 120);
                $sub->stripImage();
                $sub->trimImage(0);
                $width  = $sub->getImageWidth()  + 10;
                $height = $sub->getImageHeight() + 10;

                if ($k + 1 == $imgNum) {
                    $height += 10;
                } //最后添加10的height
                $canvas->newImage($width, $height, new ImagickPixel('white'));
                $canvas->compositeImage($sub, Imagick::COMPOSITE_DEFAULT, 5, 5);
            }

            $canvas->resetIterator();
            $outPutImgName = $path . microtime(true) . '.png';
            $canvas->appendImages(true)->writeImage($outPutImgName);
            return $outPutImgName;
        } catch (Exception $e) {
            throw new BusinessException(ErrorCode::ERROR, $e->getMessage());
        }
    }
    public static function imgToBase64($img_file)
    {
        $img_base64 = '';
        if (file_exists($img_file)) {
            $app_img_file = $img_file; // 图片路径
            $img_info     = getimagesize($app_img_file); // 取得图片的大小，类型等
            //echo '<pre>' . print_r($img_info, true) . '</pre><br>';
            $fp = fopen($app_img_file, "r"); // 图片是否可读权限
            if ($fp) {
                $filesize     = filesize($app_img_file);
                $content      = fread($fp, $filesize);
                $file_content = chunk_split(base64_encode($content)); // base64编码
                switch ($img_info[2]) {
                    //判读图片类型
                    case 1:$img_type = "gif";
                        break;
                    case 2:$img_type = "jpg";
                        break;
                    case 3:$img_type = "png";
                        break;
                }
                $img_base64 = 'data:image/' . $img_type . ';base64,' . $file_content; //合成图片的base64编码
            }
            fclose($fp);
        }
        return $img_base64; //返回图片的base64
    }
}
