<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudySession;
use App\Support\SubjectAbbreviation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $vue = in_array($request->query('vue'), ['heure', 'jour', 'semaine'], true)
            ? $request->query('vue')
            : 'semaine';

        $date = $this->parseDate($request->query('date'));
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $date->copy()->endOfWeek(Carbon::SUNDAY);
        $niveaux = $this->levels();

        $sessions = StudySession::query()
            ->with(['group.teacher', 'group.students'])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();

        $payload = $sessions->map(function (StudySession $session) use ($niveaux) {
            $group = $session->group;

            return [
                'id' => $session->id,
                'code' => $session->displayId(),
                'date' => $session->date?->format('Y-m-d'),
                'dateLabel' => $session->dateDisplay(),
                'start' => $session->heureDebutDisplay(),
                'end' => $session->heureFinDisplay(),
                'hour' => (int) substr($session->heureDebutDisplay(), 0, 2),
                'room' => $session->numero_salle,
                'statut' => $session->statut ?: StudySession::STATUT_ACTIF,
                'statutLabel' => $session->statutLabel(),
                'remarque' => $session->remarque ?: '',
                'group' => $group?->displayId(),
                'matiere' => $group?->matiere,
                'matiereLabel' => SubjectAbbreviation::display($group?->matiere),
                'niveau' => $niveaux[$group?->niveau] ?? ($group?->niveau ?: '—'),
                'teacher' => $group?->teacher?->nom_complet ?: '—',
                'effectif' => $group?->effectif() ?? 0,
                'eleves' => $group?->students->pluck('nom_complet')->values()->all() ?? [],
            ];
        })->values();

        $hours = range(7, 21);
        $weekDays = collect(range(0, 6))->map(fn (int $offset) => $weekStart->copy()->addDays($offset));

        return view('admin.calendar.index', [
            'vue' => $vue,
            'date' => $date,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'hours' => $hours,
            'weekDays' => $weekDays,
            'sessions' => $payload,
            'prevDate' => $vue === 'semaine' ? $date->copy()->subWeek()->format('Y-m-d') : $date->copy()->subDay()->format('Y-m-d'),
            'nextDate' => $vue === 'semaine' ? $date->copy()->addWeek()->format('Y-m-d') : $date->copy()->addDay()->format('Y-m-d'),
            'today' => now()->format('Y-m-d'),
        ]);
    }

    private function parseDate(?string $value): Carbon
    {
        try {
            return $value ? Carbon::parse($value)->startOfDay() : now()->startOfDay();
        } catch (\Throwable) {
            return now()->startOfDay();
        }
    }

    /**
     * @return array<string, string>
     */
    private function levels(): array
    {
        return [
            'primaire' => 'Primaire',
            'college' => 'Collège',
            'lycee' => 'Lycée',
            'coran' => 'Coran',
        ];
    }
}
