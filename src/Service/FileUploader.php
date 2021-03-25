<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param File $file
     * @param string $directory
     * @param bool $use_uuid
     * @param string $filename
     * @return array|bool
     */
    public function upload(File $file, string $directory, bool $use_uuid = true, string $filename = null)
    {
        $fileExtension = $file->guessExtension();
        if ($use_uuid){
            $uuid     = uuid_create(UUID_TYPE_RANDOM);
            $fileName = $uuid.'.'.$fileExtension;
        }elseif ($filename){
            $fileName = $filename.'.'.$fileExtension;
        }else{
            $fileName = $file->getFilename();
        }
        $path =  $directory.$fileName;
        if (!file_exists($path)){
            $file->move($directory, $fileName);
        }else{
            return false;
        }

        return array('filename' => $fileName, 'path' => $path);
    }

}