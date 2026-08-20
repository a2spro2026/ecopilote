<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudySession;
use App\Support\SubjectAbbreviation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $niveaux = [
            'primaire' => 'Primaire',
            'college' => 'Collège',
            'lycee' => 'Lycée',
            'coran' => 'Coran',
        ];

        $sessions = StudySession::query()
            ->with(['group.teacher', 'group.students'])
            ->orderByDesc('date')
            ->orderBy('heure_debut')
            ->get();

        $byRoom = [];
        foreach ($sessions as $session) {
            $code = self::normalizeRoomCode((string) $session->numero_salle);
            if ($code === null || isset($byRoom[$code])) {
                continue;
            }
            $byRoom[$code] = $session;
        }

        $salles = [];
        for ($i = 1; $i <= 20; $i++) {
            $code = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $session = $byRoom[$code] ?? null;
            $group = $session?->group;
            $statut = $session?->statut;

            $salles[] = [
                'id' => $i,
                'code' => $code,
                'nom' => 'Salle '.$code,
                'occupe' => $session !== null,
                'statut' => $statut,
                'statutLabel' => $session?->statutLabel() ?: 'Libre',
                'session' => $session ? [
                    'id' => $session->id,
                    'code' => $session->displayId(),
                    'date' => $session->dateDisplay(),
                    'start' => $session->heureDebutDisplay(),
                    'end' => $session->heureFinDisplay(),
                    'room' => $session->numero_salle,
                    'statut' => $statut,
                    'statutLabel' => $session->statutLabel(),
                    'remarque' => $session->remarque ?: '',
                    'group' => $group?->displayId(),
                    'matiere' => $group?->matiere ?: '—',
                    'matiereLabel' => SubjectAbbreviation::display($group?->matiere),
                    'niveau' => $niveaux[$group?->niveau] ?? ($group?->niveau ?: '—'),
                    'teacher' => $group?->teacher?->nom_complet ?: '—',
                    'effectif' => $group?->effectif() ?? 0,
                    'eleves' => $group?->students->pluck('nom_complet')->values()->all() ?? [],
                    'listenUrl' => route('admin.rooms.listen', $session),
                    'watchUrl' => route('admin.rooms.watch', $session),
                ] : null,
            ];
        }

        return view('admin.rooms.active', [
            'salles' => $salles,
            'occupees' => collect($salles)->where('occupe', true)->count(),
        ]);
    }

    public function listen(Request $request, StudySession $session): View
    {
        return $this->observe($request, $session, 'ecouter');
    }

    public function watch(Request $request, StudySession $session): View
    {
        return $this->observe($request, $session, 'voir');
    }

    private function observe(Request $request, StudySession $session, string $mode): View
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);
        abort_unless($session->statut === StudySession::STATUT_ACTIF, 403);
        abort_if(blank($session->numero_salle), 403);

        $session->load(['group.teacher', 'group.students']);
        $group = $session->group;
        abort_if($group === null, 404);

        $roomCode = self::normalizeRoomCode((string) $session->numero_salle);

        return view('admin.rooms.observe', [
            'mode' => $mode,
            'session' => $session,
            'roomLabel' => $roomCode !== null ? 'Salle '.$roomCode : (string) $session->numero_salle,
            'group' => $group,
            'teacher' => $group->teacher,
            'students' => $group->students,
            'matiereLabel' => SubjectAbbreviation::display($group->matiere),
        ]);
    }

    public static function normalizeRoomCode(string $raw): ?string
    {
        if (! preg_match('/(\d{1,3})\s*$/', trim($raw), $match)) {
            return null;
        }

        $number = (int) $match[1];
        if ($number < 1 || $number > 20) {
            return null;
        }

        return str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }
}
