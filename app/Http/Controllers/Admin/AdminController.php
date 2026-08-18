<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('admin.dashboard', [
            'data' => $this->mockDashboard(),
        ]);
    }

    public function page(Request $request, string $key)
    {
        $item = $this->findNavItem($key);
        abort_if(! $item, 404);

        return view('admin.page', [
            'key' => $key,
            'item' => $item,
            'group' => $item['group'] ?? '',
        ]);
    }

    public function configuration()
    {
        return view('admin.configuration', [
            'heroVideoUrl' => SiteSetting::heroVideoUrl(),
            'heroVideoMime' => SiteSetting::heroVideoMime(),
            'heroHasPicture' => SiteSetting::heroHasPicture(),
        ]);
    }

    public function storeHeroVideo(Request $request)
    {
        $request->validate([
            'video' => ['required', 'file', 'max:81920'],
        ], [
            'video.required' => 'Choisissez une vidéo à afficher dans Activités.',
            'video.max' => 'La vidéo ne doit pas dépasser 80 Mo.',
        ]);

        $file = $request->file('video');
        $originalName = strtolower((string) $file->getClientOriginalName());
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $mime = strtolower((string) ($file->getMimeType() ?: $file->getClientMimeType()));
        $allowed = [
            'mp4', 'm4v', 'webm', 'mov', 'qt', 'avi', 'mkv', 'mpeg', 'mpg', 'mpe', 'm2v',
            'ogg', 'ogv', 'wmv', 'asf', '3gp', '3g2', 'flv', 'f4v', 'ts', 'm2ts', 'mts', 'vob',
        ];

        $hasVideoExtension = in_array($extension, $allowed, true);
        $hasVideoMime = str_starts_with($mime, 'video/')
            || str_contains($mime, 'mp4')
            || str_contains($mime, 'mpeg')
            || str_contains($mime, 'quicktime')
            || str_contains($mime, 'matroska')
            || str_contains($mime, 'webm')
            || in_array($mime, ['application/octet-stream', 'application/ogg', 'binary/octet-stream'], true);

        if (! $hasVideoExtension && ! $hasVideoMime) {
            return back()->withErrors([
                'video' => 'Choisissez un fichier vidéo (MP4, WebM, MOV, AVI, MKV…).',
            ]);
        }

        if (\App\Support\VideoFile::isAudioOnly((string) $file->getRealPath())) {
            return back()->withErrors([
                'video' => 'Ce fichier n’a pas d’image. Choisissez une vraie vidéo (caméra, film, MP4 avec image), pas un fichier audio.',
            ]);
        }

        $previous = SiteSetting::getValue(SiteSetting::HERO_VIDEO);
        $path = $request->file('video')->store('site', 'public');
        SiteSetting::setValue(SiteSetting::HERO_VIDEO, $path);

        if ($previous && $previous !== $path) {
            Storage::disk('public')->delete($previous);
        }

        return redirect()
            ->route('admin.page.configuration')
            ->with('status', 'Vidéo enregistrée. Elle est disponible dans Activités.');
    }

    public function destroyHeroVideo()
    {
        $previous = SiteSetting::getValue(SiteSetting::HERO_VIDEO);

        if ($previous) {
            Storage::disk('public')->delete($previous);
        }

        SiteSetting::setValue(SiteSetting::HERO_VIDEO, null);

        return redirect()
            ->route('admin.page.configuration')
            ->with('status', 'Vidéo retirée de la page Activités.');
    }

    private function findNavItem(string $key): ?array
    {
        foreach (config('admin.navigation', []) as $section) {
            foreach ($section['items'] as $item) {
                if (($item['key'] ?? null) === $key) {
                    return array_merge($item, ['group' => $section['group']]);
                }
            }
        }

        return null;
    }

    /**
     * Données fictives pour le design du Centre de contrôle.
     */
    private function mockDashboard(): array
    {
        return [
            'today' => now()->locale('fr')->isoFormat('dddd D MMMM YYYY'),

            'stats' => [
                ['label' => 'Élèves actifs', 'value' => '0', 'hint' => 'Aucun élève', 'up' => false, 'tone' => 'blue', 'icon' => 'users'],
                ['label' => 'Professeurs actifs', 'value' => '0', 'hint' => 'Aucun professeur', 'up' => false, 'tone' => 'emerald', 'icon' => 'teacher'],
                ['label' => "Séances aujourd'hui", 'value' => '0', 'hint' => 'Aucune séance', 'up' => false, 'tone' => 'indigo', 'icon' => 'calendar'],
                ['label' => 'Séances en direct', 'value' => '0', 'hint' => 'Aucune', 'up' => false, 'tone' => 'green', 'icon' => 'live'],
                ['label' => 'Demandes en attente', 'value' => '0', 'hint' => 'Aucune demande', 'up' => false, 'tone' => 'amber', 'icon' => 'inbox'],
                ['label' => 'Revenus du mois', 'value' => '0', 'hint' => 'MAD', 'up' => false, 'tone' => 'violet', 'icon' => 'money'],
            ],

            'sessions_today' => [],

            'activity' => [],

            'week_days' => ['Lun 11', 'Mar 12', 'Mer 13', 'Jeu 14', 'Ven 15', 'Sam 16', 'Dim 17'],
            'week_slots' => ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00'],
            'week_events' => [],

            'archives' => [],
        ];
    }

    public static function pageKeys(): array
    {
        $keys = [];
        foreach (config('admin.navigation', []) as $section) {
            foreach ($section['items'] as $item) {
                if (! empty($item['route'])) {
                    continue;
                }
                $keys[] = $item['key'];
            }
        }

        return $keys;
    }
}
