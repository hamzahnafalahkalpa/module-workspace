<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

if (!function_exists('workspace_url')) {
    /**
     * Generate asset URL, compatible with local/public and S3.
     */
    function workspace_url(string $url): string
    {
        $disk = config('filesystems.default', 'public');
        $base = rtrim(config('filesystems.asset_url', '/workspaces'), '/');
        $path = ltrim($url, '/');

        // Kalau disk-nya S3, generate via Storage
        if ($disk === 's3') {
            return Storage::disk('s3')->url($path);
        }

        // Selain S3, tetap pakai asset() lokal
        return asset($base . '/' . $path);
    }
}

if (! function_exists('workspace_identifier')) {
    function workspace_identifier($name) {
        $cache_driver = config('cache.default');
        switch ($cache_driver) {
            case 'redis':
                
            break;
            default:
            break;
        }
    }
}