<?php

namespace App\Service;


use App\Repository\FilesRepository;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FilePath
{
    private $filesDir;
    private $filesRepository;

    public function __construct(string $filesDir, FilesRepository $filesRepository)
    {
        $this->filesDir = $filesDir;
        $this->filesRepository = $filesRepository;
    }

    public function get(int $id): ?string
    {
        $slugger = new AsciiSlugger();
        $mimeTypes = new MimeTypes();
        $file = $this->filesRepository->find($id);

        $date = $file->getUploadDate();
        $dir = $this->getDir($date);

        $name = $file->getName();
        $fileName = $slugger->slug($name);

        $mime = $file->getMime();
        $fileExtensions = $mimeTypes->getExtensions($mime);
        foreach ($fileExtensions as $extension){
            $path = $dir.$fileName.'.'.$extension;
             if (file_exists($path)){
                 return $path;
                 continue;
             }
        }
        return null;
    }

    public function getDir(\DateTimeInterface $date): string
    {
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');
        return "{$this->filesDir}/{$year}/{$month}/{$day}/";
    }

    public function getThumbnail(int $id): ?string
    {
        $slugger = new AsciiSlugger();
        $mimeTypes = new MimeTypes();
        $file = $this->filesRepository->find($id);

        $date = $file->getUploadDate();
        $dir = $this->getDir($date);

        $fileName = $slugger->slug($file->getName());

        $mime = $file->getMime();
        $fileExtensions = $mimeTypes->getExtensions($mime);
        if ($dir && $fileName && $fileExtensions){
            return "{$dir}{$fileName}-150x150.{$fileExtensions[0]}";
        }
        return null;
    }

    public function generate(string $name, \DateTimeInterface $date, string $mime): string
    {
        $slugger = new AsciiSlugger();
        $mimeTypes = new MimeTypes();
        $dir = $this->getDir($date);
        $fileName = $slugger->slug($name);
        $fileExtensions = $mimeTypes->getExtensions($mime);
        return $dir.$fileName.'.'.$fileExtensions[0];
    }

    public function generateThumbnail(string $name, \DateTimeInterface $date, string $mime): ?string
    {
        $slugger = new AsciiSlugger();
        $mimeTypes = new MimeTypes();

        $dir = $this->getDir($date);

        $fileName = $slugger->slug($name);
        $fileExtensions = $mimeTypes->getExtensions($mime);
        if ($dir && $fileName && $fileExtensions){
            return "{$dir}{$fileName}-150x150.{$fileExtensions[0]}";
        }
        return null;
    }
}