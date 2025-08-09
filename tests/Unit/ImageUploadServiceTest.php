<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadServiceTest extends TestCase
{
    public function test_upload_image_returns_path()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');
        $service = new \App\Services\ImageUploadService();
        $path = $service->upload($file, 'users', 'user');
        $this->assertNotEmpty($path);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($path));
    }
}
