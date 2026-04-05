<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadService
{
    public function __construct(private readonly string $uploadDir)
    {
    }

    public function upload(?UploadedFile $file): ?string
    {
        if ($file === null) {
            return null;
        }

        $filename = uniqid('thread_', true).'.'.$file->guessExtension();
        $file->move($this->uploadDir, $filename);

        return '/uploads/'.$filename;
    }
}
