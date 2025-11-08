<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommonHelper
{
   
    public static function uploadFile(?UploadedFile $file, string $folder = 'uploads', string $disk = 'public'): ?string
    {
        if (!$file) {
            return null;
        }

        try {

            $storage = Storage::disk($disk);
            
            // Ensure folder exists (create if not)
            if (!$storage->exists($folder)) {
                $storage->makeDirectory($folder);
            }
            
            // Create unique filename
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Save file to the given disk and folder
            $path = $file->storeAs($folder, $filename, $disk);

            // Return the stored file path
            return $path;
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return null;
        }
    }

    
    public static function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        return false;
    }

    public static function getFullPath(?string $path, string $disk = 'public'): ?string
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            return asset('storage/' . $path);
        }
        return null;
    }


    public static function viewUploadedfile(?string $path, string $disk = 'public'): ?string
    {
        $fullPath = self::getFullPath($path, $disk);
        if ($fullPath) {
            return '<div class="mt-2"><a href="' . $fullPath . '" target="_blank">View Uploaded File</a></div>';
        }
        return null;
    }
}
