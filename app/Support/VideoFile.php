<?php

namespace App\Support;

class VideoFile
{
    public static function isAudioOnly(?string $absolutePath): bool
    {
        if (! $absolutePath || ! is_file($absolutePath)) {
            return false;
        }

        $contents = @file_get_contents($absolutePath, false, null, 0, 4_000_000) ?: '';

        if (! str_contains($contents, 'ftyp')) {
            return false;
        }

        if (self::hasVideoMarkers($contents)) {
            return false;
        }

        return str_contains($contents, 'smhd') || str_contains($contents, 'soun');
    }

    public static function hasPicture(?string $absolutePath): bool
    {
        return $absolutePath !== null
            && is_file($absolutePath)
            && ! self::isAudioOnly($absolutePath);
    }

    private static function hasVideoMarkers(string $contents): bool
    {
        foreach (['vmhd', 'avc1', 'hev1', 'hvc1', 'vp09', 'av01', 'mp4v', 'VP80', 'VP90'] as $marker) {
            if (str_contains($contents, $marker)) {
                return true;
            }
        }

        return false;
    }
}
