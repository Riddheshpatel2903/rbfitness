<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class MediaHelper
{
    /**
     * Upload a file to Cloudinary if URL is set, otherwise fall back to local disk.
     */
    public static function upload($file, $folder = 'general')
    {
        $cloudinaryUrl = config('cloudinary.url');

        if ($cloudinaryUrl) {
            try {
                $cloudinary = new Cloudinary($cloudinaryUrl);
                $upload = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'rbfitness/' . $folder,
                    'resource_type' => 'auto',
                ]);
                return $upload['secure_url'];
            } catch (\Exception $e) {
                \Log::error('Cloudinary upload failed: ' . $e->getMessage());
            }
        }

        // Default Local Storage (Shared Hosting compatible)
        // Ensure required directories exist
        if (!Storage::disk('public')->exists('trainers')) {
            Storage::disk('public')->makeDirectory('trainers');
        }
        if (!Storage::disk('public')->exists('facilities')) {
            Storage::disk('public')->makeDirectory('facilities');
        }

        // Store file and return relative path (e.g., trainers/abc.png)
        return $file->store($folder, 'public');
    }

    /**
     * Delete a file from storage.
     */
    public static function delete($path)
    {
        if (!$path) return;

        if (str_contains($path, 'cloudinary')) {
            return;
        }

        // Local deletion - handle both full URLs (legacy) and relative paths
        $cleanPath = str_replace(asset('storage/'), '', $path);
        $cleanPath = ltrim($cleanPath, '/');
        
        Storage::disk('public')->delete($cleanPath);
    }

    /**
     * Helper to get the correct URL for a path
     */
    public static function getUrl($path)
    {
        if (!$path) return asset('images/placeholder.png');

        if (str_contains($path, 'http')) {
            return $path;
        }

        return asset('storage/' . $path);
    }
}
