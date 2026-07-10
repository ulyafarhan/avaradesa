<?php

namespace App\Services;

/**
 * Service untuk memproses dan mengompres gambar.
 */
class ImageService
{
    /**
     * Mengompres gambar ke format WebP dengan resize otomatis.
     *
     * @param string $sourcePath Path asal file gambar.
     * @param string|null $destPath Path tujuan (null = ganti ekstensi asli dengan .webp).
     * @param int $maxDimension Dimensi maksimal (pixel) untuk resize.
     * @param int $quality Kualitas kompresi (0-100).
     * @return string|null Path file hasil kompresi, atau null jika gagal.
     */
    public static function compressToWebP(string $sourcePath, ?string $destPath = null, int $maxDimension = 1600, int $quality = 80): ?string
    {
        if (!file_exists($sourcePath)) {
            return null;
        }

        if (!function_exists('imagewebp') || !(gd_info()['WebP Support'] ?? false)) {
            return $sourcePath;
        }

        $info = getimagesize($sourcePath);
        if (!$info) {
            return null;
        }

        $mime = $info['mime'];
        $width = $info[0];
        $height = $info[1];

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($sourcePath);
                break;
            default:
                return null;
        }

        if (!$image) {
            return null;
        }

        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width > $height) {
                $newWidth = $maxDimension;
                $newHeight = (int) round(($height * $maxDimension) / $width);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) round(($width * $maxDimension) / $height);
            }

            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);

            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $newImage;
        }

        if (!$destPath) {
            $destPath = preg_replace('/\.[^.]+$/', '.webp', $sourcePath);
        }

        $success = imagewebp($image, $destPath, $quality);
        imagedestroy($image);

        if ($success) {
            if ($sourcePath !== $destPath && file_exists($sourcePath)) {
                @unlink($sourcePath);
            }
            return $destPath;
        }

        return $sourcePath;
    }
}
