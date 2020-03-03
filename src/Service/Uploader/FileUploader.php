<?php

declare(strict_types=1);

namespace App\Service\Uploader;

class FileUploader
{
    /**
     * @param $uploadDir
     * @param $file
     * @return string
     */
    public function upload(string $uploadDir, object $file): string
    {
        $fileName = $file->getClientOriginalName();
        $file->move($uploadDir, $fileName);

        return $uploadDir.'/'.$fileName;
    }
}
