<?php


namespace App\Service;

use Symfony\Component\Mime\MimeTypes;

class ImageResizer
{
    public function thuhmbnail(string $path): string
    {
            $fileInfos = pathinfo($path);
            $fileName = $fileInfos['filename'];
            $fileExt = $fileInfos['extension'];
            $fileDir = $fileInfos['dirname'];
            $height = 150;
            $width = 150;
            $quality = 75;
            $image_size     = getimagesize($path);
            $image_width    = $image_size[0];
            $image_height   = $image_size[1];
            $image_ratio    = $image_width / $image_height;
            if ($image_width > $image_height){
                $width = round($image_ratio * $height);
            }
            $newPath = "{$fileDir}/{$fileName}-150x150.{$fileExt}";
            $mimeTypes = new MimeTypes();
            $mimeTypes = $mimeTypes->guessMimeType($path);
            switch ($mimeTypes) {
                case 'image/jpeg':
                case 'image/jpg':
                    $img_ressources = imagecreatefromjpeg($path);
                    $img_resize = imagescale($img_ressources, $width);
                    $img_resize = imagecrop($img_resize,['x' => (($width - 150) / 2), 'y' => (($height - 150) / 2), 'width' => 150, 'height' => $height]);
                    imagejpeg($img_resize, $newPath, $quality);
                    break;
                case 'image/png':
                    $img_ressources = imagecreatefrompng($path);
                    $img_resize = imagescale($img_ressources, $width);
                    $img_resize = imagecrop($img_resize,['x' => (($width - 150) / 2), 'y' => (($height - 150) / 2), 'width' => 150, 'height' => $height ]);
                    imagepng($img_resize, $newPath, $quality);
                    break;
                case 'image/gif':
                    $img_ressources = imagecreatefromgif($path);
                    $img_resize = imagescale($img_ressources, $width);
                    $img_resize = imagecrop($img_resize,['x' => (($width - 150) / 2), 'y' => (($height - 150) / 2), 'width' => 150, 'height' => $height ]);
                    imagegif($img_resize, $newPath);
                    break;
                default:
                    return false;
                    break;
            }
            return $newPath;
    }
    public function width(int $width, string $path, int $height = null, int $quality = 75)
    {
            $fileInfos = pathinfo($path);
            $fileName = $fileInfos['filename'];
            $fileExt = $fileInfos['extension'];
            $fileDir = $fileInfos['dirname'];
            $image_size     = getimagesize($path);
            $image_width    = $image_size[0];
            $image_height   = $image_size[1];
            $image_ratio    = $image_width / $image_height;
            if (!$height){
                $height = round($width * $image_ratio);
            }
            $newPath = "{$fileDir}/{$fileName}-{$width}x{$height}.{$fileExt}";
            $mimeTypes = new MimeTypes();
            $mimeTypes = $mimeTypes->guessMimeType($path);
            switch ($mimeTypes) {
                case 'image/jpeg':
                case 'image/jpg':
                    $img_ressources = imagecreatefromjpeg($path);
                    $img_resize = imagescale($img_ressources, $width);
                    $img_resize = imagecrop($img_resize,['x' => 0, 'y' => 0, 'width' => $width, 'height' => $height / 2]);
                    imagejpeg($img_resize, $newPath, $quality);
                    break;
                case 'image/png':
                    $img_ressources = imagecreatefrompng($path);
                    $img_resize = imagescale($img_ressources, $width);
                    $img_resize = imagecrop($img_resize,['x' => 0, 'y' => 0, 'width' => $width, 'height' => $height / 2]);
                    imagepng($img_resize, $newPath, $quality);
                    break;
                default:
                    return false;
                    break;
            }
            return $newPath;
    }
}