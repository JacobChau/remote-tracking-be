<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadImageToS3 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $imageUrl;

    protected string $fileName;

    /**
     * Create a new job instance.
     */
    public function __construct(string $imageUrl, string $fileName)
    {
        $this->imageUrl = $imageUrl;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileContents = file_get_contents($this->imageUrl);
        if ($fileContents !== false) {
            Storage::disk('s3')->put($this->fileName, $fileContents, 'public');
        }
    }
}
