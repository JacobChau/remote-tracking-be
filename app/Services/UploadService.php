<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\UploadImageToS3;

class UploadService
{
    public function uploadToS3(string $imageUrl): string
    {
        $fileName = basename($imageUrl);

        UploadImageToS3::dispatch($imageUrl, $fileName);

        return $fileName;
    }
}
