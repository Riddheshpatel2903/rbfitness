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
        $cloudinaryUrl = env('CLOUDINARY_URL');

        if ($cloudinaryUrl) {
            try {
                // Initialize Cloudinary from the environment variable (cloudinary://key:secret@name)
                $cloudinary = new Cloudinary($cloudinaryUrl);

                $upload = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'rbfitness/' . $folder,
                    'resource_type' => 'auto', // Detect image or video
                ]);

                return $upload['secure_url'];
            } catch (\Exception $e) {
                // Fall back to local if Cloudinary fails
                \Log::error('Cloudinary upload failed: ' . $e->getMessage());
            }
        }

        // Default Local Storage (Ephemeral on Render)
        $path = $file->store($folder, 'public');
        return asset('storage/' . $path);
    }

    /**
     * Note: We usually don't delete from Cloudinary automatically in a simple implementation,
     * but we provide this for consistency.
     */
    public static function delete($url)
    {
        if (str_contains($url, 'cloudinary')) {
            // Deleting from cloudinary requires parsing the public_id, 
            // commonly skipped in MVPs to avoid complex regex.
            return;
        }

        // Local deletion
        $path = str_replace(asset('storage/'), '', $url);
        Storage::disk('public')->delete($path);
    }
}
