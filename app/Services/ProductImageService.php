<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function upload(UploadedFile $image): string
    {
        $path = $image->store('products', 'public');

        if ($path === false) {
            throw new \RuntimeException('Product image upload failed.');
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if (
            $path === null
            || ! preg_match(
                '~\Aproducts/[^/\\\\]+\.(?:jpg|jpeg|png|webp)\z~i',
                $path
            )
        ) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
