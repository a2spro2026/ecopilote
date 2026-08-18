<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    public const HERO_VIDEO = 'hero_video_path';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        if (! Schema::hasTable('site_settings')) {
            return $default;
        }

        $row = static::query()->where('key', $key)->first();

        return $row?->value ?? $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function heroVideoPath(): ?string
    {
        $path = static::getValue(self::HERO_VIDEO);

        if (! $path) {
            return null;
        }

        return ltrim(str_replace('\\', '/', $path), '/');
    }

    public static function heroVideoUrl(): ?string
    {
        $path = static::heroVideoPath();

        if (! $path) {
            return null;
        }

        return route('site.hero-video', ['v' => substr(sha1($path), 0, 10)]);
    }

    public static function heroHasPicture(): bool
    {
        $path = static::heroVideoPath();

        if (! $path) {
            return false;
        }

        return \App\Support\VideoFile::hasPicture(Storage::disk('public')->path($path));
    }

    public static function heroVideoMime(): string
    {
        $path = static::heroVideoPath() ?? '';
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'webm' => 'video/webm',
            'ogg', 'ogv' => 'video/ogg',
            'mov', 'qt' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'mkv' => 'video/x-matroska',
            default => 'video/mp4',
        };
    }
}
