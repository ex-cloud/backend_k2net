<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasSlugAndImage
{
    /**
     * Generate slug dari title jika slug kosong.
     */
    public function generateSlug(?string $slug, string $title): string
    {
        return $slug ? $slug : Str::slug($title);
    }

    /**
     * Simpan gambar jika ada, atau gunakan default.
     *
     * @param UploadedFile|null $image
     * @param string $folder
     * @param string $default
     * @return string $imageName
     */
    public function storeImage(?UploadedFile $image, string $folder, string $default = 'default.png'): string
    {
        if ($image) {
            $imageName = $image->hashName();
            $image->storeAs($folder, $imageName, 'public');
            return $imageName;
        }

        return $default;
    }

    /**
     * Hapus gambar lama jika ada.
     *
     * @param string|null $image
     * @param string $folder
     * @return void
     */
    public function deleteOldImage(?string $image, string $folder): void
    {
        if ($image && Storage::disk('public')->exists("$folder/$image")) {
            Storage::disk('public')->delete("$folder/$image");
        }
    }
}
