<?php

namespace Tests\Unit\UploadService;

use App\Jobs\UploadImageToS3;
use App\Services\UploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(UploadService::class);
    }

    public function testUploadToS3()
    {
        Storage::fake('s3');
        $imageUrl = 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png';
        $fileName = 'googlelogo_color_272x92dp.png';

        Http::fake([
            $imageUrl => Http::response('image_contents'),
        ]);

        $job = new UploadImageToS3($imageUrl, $fileName);
        $job->handle();

        Storage::disk('s3')->assertExists($fileName);

        $this->assertEquals('googlelogo_color_272x92dp.png', $this->service->uploadToS3($imageUrl));
    }
}
