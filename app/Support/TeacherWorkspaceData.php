<?php

namespace App\Support;

use App\Http\Controllers\Admin\RoomController;
use App\Models\StudyGroup;
use App\Models\StudySession;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TeacherWorkspaceData
{
    public function __construct(private readonly Teacher $teacher) {}

    public static function for(Teacher $teacher): self
    {
        return new self($teacher);
    }

    public function kpis(): array
    {
        $groups = StudyGroup::query()
            ->where('teacher_id', $this->teacher->id)
            ->withCount('students')
            ->get();

        $groupCount = $groups->count();
        $studentCount = $groups->sum('students_count');
        $monthSessions = $this->sessionsQuery()
            ->whereBetween('date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
            ->count();

        return [
            ['key' => 'groupes', 'label' => 'Mes Groupes', 'value' => (string) $groupCount, 'hint' => $groupCount === 1 ? '1 groupe actif' : ($groupCount > 1 ? $groupCount.' groupes actifs' : 'Aucun groupe'), 'up' => $groupCount > 0, 'tone' => 'emerald', 'icon' => 'groups', 'suffix' => ''],
            ['key' => 'eleves', 'label' => 'Mes élèves', 'value' => (string) $studentCount, 'hint' => $studentCount === 1 ? '1 élève' : ($studentCount > 1 ? $studentCount.' élèves' : 'Aucun élève'), 'up' => $studentCount > 0, 'tone' => 'blue', 'icon' => 'users', 'suffix' => ''],
            ['key' => 'seances', 'label' => 'Séances / Mois', 'value' => (string) $monthSessions, 'hint' => 'Ce mois-ci', 'up' => $monthSessions > 0, 'tone' => 'indigo', 'icon' => 'calendar', 'suffix' => ''],
            ['key' => 'revenu', 'label' => 'Total Revenue / Mois', 'value' => '0', 'hint' => 'MAD ce mois', 'up' => false, 'tone' => 'violet', 'icon' => 'money', 'suffix' => 'MAD'],
            ['key' => 'solde', 'label' => 'Solde', 'value' => '0', 'hint' => 'Disponible', 'up' => false, 'tone' => 'amber', 'icon' => 'wallet', 'suffix' => 'MAD'],
        ];
    }

    public function nextSession(): array
    {
        $session = $this->sessionsQuery()
            ->where('statut', StudySession::STATUT_ACTIF)
            ->whereNotNull('numero_salle')
            ->where('numero_salle', '!=', '')
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->first();

        if ($session === null) {
            return [
                'id' => null,
                'classe_id' => 0,
                'matiere' => $this->teacher->matiere ?: 'Cours',
                'salle' => 'Aucune salle',
                'debut' => '—',
                'fin' => '—',
                'effectif' => 0,
                'statut' => 'prete',
                'statut_label' => 'Aucune séance prévue',
                'joinable' => false,
            ];
        }

        return $this->toRoomSession($session);
    }

    public function classes(): array
    {
        return StudyGroup::query()
            ->where('teacher_id', $this->teacher->id)
            ->withCount('students')
            ->orderBy('matiere')
            ->get()
            ->map(fn (StudyGroup $group) => [
                'id' => $group->id,
                'matiere' => $group->matiere,
                'niveau' => $group->niveau,
                'effectif' => $group->students_count,
                'salle' => $group->displayId(),
            ])
            ->all();
    }

    public function sessions(?string $filter = 'toutes'): array
    {
        $query = $this->sessionsQuery();
        $this->applySessionFilter($query, $filter);

        return $query
            ->get()
            ->map(fn (StudySession $session) => $this->toSeanceRow($session))
            ->all();
    }

    public function sessionsToday(): array
    {
        return $this->sessionsQuery()
            ->whereDate('date', now()->toDateString())
            ->orderBy('heure_debut')
            ->get()
            ->map(fn (StudySession $session) => $this->toSeanceRow($session))
            ->all();
    }

    public function sessionForTeacher(int $sessionId): ?StudySession
    {
        return $this->sessionsQuery()->whereKey($sessionId)->first();
    }

    public function canJoin(StudySession $session): bool
    {
        if ($session->statut !== StudySession::STATUT_ACTIF) {
            return false;
        }

        if (blank($session->numero_salle)) {
            return false;
        }

        return $session->group?->teacher_id === $this->teacher->id;
    }

    public function toRoomSession(StudySession $session): array
    {
        $group = $session->group;
        $uiStatut = $this->uiStatut($session);
        $roomCode = RoomController::normalizeRoomCode((string) $session->numero_salle);
        $salleLabel = $roomCode !== null
            ? 'Salle '.$roomCode
            : (string) $session->numero_salle;

        return [
            'id' => $session->id,
            'classe_id' => $group?->id ?? 0,
            'code' => $session->displayId(),
            'matiere' => $group?->matiere ?: ($this->teacher->matiere ?: 'Cours'),
            'salle' => $salleLabel,
            'debut' => $session->heureDebutDisplay(),
            'fin' => $session->heureFinDisplay(),
            'effectif' => $group?->students->count() ?? 0,
            'statut' => $uiStatut,
            'statut_label' => $this->uiStatutLabel($session, $uiStatut),
            'joinable' => $this->canJoin($session),
        ];
    }

    public function roomStudents(StudySession $session): array
    {
        return $session->group?->students
            ->map(fn ($student) => [
                'id' => $student->id,
                'nom' => $student->nom_complet,
                'etat' => 'deconnecte',
            ])
            ->all() ?? [];
    }

    private function sessionsQuery(): Builder
    {
        return StudySession::query()
            ->with(['group.students', 'group.teacher'])
            ->whereHas('group', fn (Builder $query) => $query->where('teacher_id', $this->teacher->id))
            ->orderByDesc('date')
            ->orderBy('heure_debut');
    }

    private function applySessionFilter(Builder $query, string $filter): void
    {
        match ($filter) {
            'aujourdhui' => $query->whereDate('date', now()->toDateString()),
            'semaine' => $query->whereBetween('date', [
                now()->startOfWeek(Carbon::MONDAY)->toDateString(),
                now()->endOfWeek(Carbon::SUNDAY)->toDateString(),
            ]),
            'mois' => $query->whereBetween('date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ]),
            default => null,
        };
    }

    private function toSeanceRow(StudySession $session): array
    {
        $group = $session->group;
        $uiStatut = $this->uiStatut($session);

        return [
            'id' => $session->id,
            'date' => $session->dateDisplay(),
            'heure' => $session->heureDebutDisplay().' — '.$session->heureFinDisplay(),
            'classe' => $group?->displayId() ?: '—',
            'matiere' => $group?->matiere ?: '—',
            'eleves' => $group?->students->count() ?? 0,
            'professeur' => $group?->teacher?->nom_complet ?: $this->teacher->nom_complet,
            'salle' => $session->numero_salle ?: '—',
            'statut' => $uiStatut,
            'joinable' => $this->canJoin($session),
        ];
    }

    private function uiStatut(StudySession $session): string
    {
        if ($session->statut === StudySession::STATUT_ANNULEE) {
            return 'annulee';
        }

        if ($session->statut === StudySession::STATUT_REPORTEE) {
            return 'annulee';
        }

        $date = $session->date?->toDateString();
        $today = now()->toDateString();

        if ($date === null) {
            return 'a_venir';
        }

        if ($date > $today) {
            return 'a_venir';
        }

        if ($date < $today) {
            return 'terminee';
        }

        $now = now()->format('H:i');
        $start = $session->heureDebutDisplay();
        $end = $session->heureFinDisplay();

        if ($now < $start) {
            return 'a_venir';
        }

        if ($now > $end) {
            return 'terminee';
        }

        return 'en_direct';
    }

    private function uiStatutLabel(StudySession $session, string $uiStatut): string
    {
        if ($session->statut === StudySession::STATUT_REPORTEE) {
            return 'Séance reportée';
        }

        if ($session->statut === StudySession::STATUT_ANNULEE) {
            return 'Séance annulée';
        }

        return match ($uiStatut) {
            'en_direct' => 'En direct',
            'a_venir' => 'À venir',
            'terminee' => 'Terminée',
            default => $session->statutLabel(),
        };
    }
}
