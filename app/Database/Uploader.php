<?php

namespace App\Database;

class Uploader
{
    public static function uploadProductImage(array $file): ?string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Validasi ekstensi dan mime type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes)) {
            return null;
        }

        $targetDir = dirname(__DIR__, 2) . '/public/assets/images/products/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
        $targetPath = $targetDir . $filename;

        // Optimasi: Resize & kompres menggunakan GD jika tersedia
        if (extension_loaded('gd')) {
            $srcImage = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($file['tmp_name']),
                'image/png'  => @imagecreatefrompng($file['tmp_name']),
                'image/webp' => @imagecreatefromwebp($file['tmp_name']),
                default      => null
            };

            if ($srcImage) {
                $origWidth = imagesx($srcImage);
                $origHeight = imagesy($srcImage);

                // Max dimension 800px (sangat optimal & hemat storage untuk HP)
                $maxDim = 800;
                if ($origWidth > $maxDim || $origHeight > $maxDim) {
                    if ($origWidth >= $origHeight) {
                        $newWidth = $maxDim;
                        $newHeight = (int) round(($origHeight / $origWidth) * $maxDim);
                    } else {
                        $newHeight = $maxDim;
                        $newWidth = (int) round(($origWidth / $origHeight) * $maxDim);
                    }
                } else {
                    $newWidth = $origWidth;
                    $newHeight = $origHeight;
                }

                $dstImage = imagecreatetruecolor($newWidth, $newHeight);
                // Preserve transparency
                imagealphablending($dstImage, false);
                imagesavealpha($dstImage, true);

                imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                // Simpan dalam format WebP dengan kualitas 80 (ringan & jernih)
                imagewebp($dstImage, $targetPath, 80);
                imagedestroy($srcImage);
                imagedestroy($dstImage);

                return $filename;
            }
        }

        // Fallback jika GD gagal / gambar tidak terbaca
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fallbackFilename = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . ($ext ?: 'jpg');
        move_uploaded_file($file['tmp_name'], $targetDir . $fallbackFilename);
        return $fallbackFilename;
    }
}
