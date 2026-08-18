<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SiteMediaController extends Controller
{
    public function heroVideo(): BinaryFileResponse
    {
        $path = SiteSetting::heroVideoPath();
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type' => SiteSetting::heroVideoMime(),
            'Content-Disposition' => 'inline; filename="accueil.mp4"',
        ]);
    }
}
