<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageServiceTest extends TestCase
{
    public function test_it_compresses_and_converts_image_to_webp_successfully()
    {
        // 1. Create a fake image file
        Storage::fake('public');
        $file = UploadedFile::fake()->image('cover_test.jpg', 2000, 1000); // Exceeds 1600px
        
        // Save fake image locally for test
        $tempDir = sys_get_temp_dir();
        $tempSourcePath = $tempDir . '/test_source_' . time() . '.jpg';
        file_put_contents($tempSourcePath, file_get_contents($file->getRealPath()));

        $this->assertTrue(file_exists($tempSourcePath));

        // 2. Perform compression
        $tempDestPath = $tempDir . '/test_dest_' . time() . '.webp';
        $result = ImageService::compressToWebP($tempSourcePath, $tempDestPath, 1600, 80);

        // 3. Asserts
        $this->assertNotNull($result);
        $this->assertTrue(file_exists($tempDestPath));
        
        // Verify output file type & dimensions
        $info = getimagesize($tempDestPath);
        $this->assertEquals('image/webp', $info['mime']);
        
        // Since original width was 2000 and height was 1000, max dimension of 1600 should scale it to 1600x800
        $this->assertEquals(1600, $info[0]);
        $this->assertEquals(800, $info[1]);

        // Clean up temp files
        @unlink($tempSourcePath);
        @unlink($tempDestPath);
    }
}
