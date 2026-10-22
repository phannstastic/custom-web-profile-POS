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

    /**
     * Upload banyak foto produk sekaligus (gallery_images)
     * @param array $filesArray Dari $_FILES['images'] yang berisi array 'name', 'type', 'tmp_name', 'error', 'size'
     * @return array List nama file gambar yang berhasil disimpan
     */
    public static function uploadMultipleProductImages(array $filesArray): array
    {
        $uploaded = [];
        if (!isset($filesArray['name']) || !is_array($filesArray['name'])) {
            return $uploaded;
        }

        $total = count($filesArray['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($filesArray['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $singleFile = [
                'name'     => $filesArray['name'][$i],
                'type'     => $filesArray['type'][$i],
                'tmp_name' => $filesArray['tmp_name'][$i],
                'error'    => $filesArray['error'][$i],
                'size'     => $filesArray['size'][$i],
            ];

            $savedFilename = self::uploadProductImage($singleFile);
            if ($savedFilename) {
                $uploaded[] = $savedFilename;
            }
        }

        return $uploaded;
    }

    public static function uploadAsset(array $file, string $prefix = 'asset_'): ?string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes)) {
            return null;
        }

        $targetDir = dirname(__DIR__, 2) . '/public/assets/uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($mime === 'image/svg+xml') {
            $filename = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.svg';
            move_uploaded_file($file['tmp_name'], $targetDir . $filename);
            return $filename;
        }

        $filename = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . ($ext ?: 'png');
        move_uploaded_file($file['tmp_name'], $targetDir . $filename);
        return $filename;
    }
}
