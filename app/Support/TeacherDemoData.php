<?php

namespace App\Support;

use App\Models\Teacher;

class TeacherDemoData
{
    public function __construct(private readonly Teacher $teacher) {}

    public function professor(): array
    {
        return [
            'nom' => $this->teacher->nom_complet,
            'matiere' => $this->teacher->matiere ?: '—',
            'code' => $this->teacher->displayId(),
            'etat' => 'validé',
        ];
    }

    public function kpis(): array
    {
        return [
            ['key' => 'groupes', 'label' => 'Mes Groupes', 'value' => '0', 'hint' => 'Aucun groupe', 'up' => false, 'tone' => 'emerald', 'icon' => 'groups', 'suffix' => ''],
            ['key' => 'eleves', 'label' => 'Mes élèves', 'value' => '0', 'hint' => 'Aucun élève', 'up' => false, 'tone' => 'blue', 'icon' => 'users', 'suffix' => ''],
            ['key' => 'seances', 'label' => 'Séances / Mois', 'value' => '0', 'hint' => 'Ce mois-ci', 'up' => false, 'tone' => 'indigo', 'icon' => 'calendar', 'suffix' => ''],
            ['key' => 'revenu', 'label' => 'Total Revenue / Mois', 'value' => '0', 'hint' => 'MAD ce mois', 'up' => false, 'tone' => 'violet', 'icon' => 'money', 'suffix' => 'MAD'],
            ['key' => 'solde', 'label' => 'Solde', 'value' => '0', 'hint' => 'Disponible', 'up' => false, 'tone' => 'amber', 'icon' => 'wallet', 'suffix' => 'MAD'],
        ];
    }

    public function nextSession(): array
    {
        return [
            'id' => 'salle-vide',
            'classe_id' => 0,
            'matiere' => $this->teacher->matiere ?: 'Cours',
            'salle' => 'Aucune salle',
            'debut' => '—',
            'fin' => '—',
            'effectif' => 0,
            'statut' => 'prete',
            'statut_label' => 'Aucune séance prévue',
        ];
    }

    public function classes(): array
    {
        return [];
    }

    public function classById(int $id): ?array
    {
        return collect($this->classes())->firstWhere('id', $id);
    }

    public function sessions(?string $filter = 'toutes'): array
    {
        return [];
    }

    public function sessionsToday(): array
    {
        return [];
    }

    public function students(?int $classeId = null): array
    {
        return [];
    }

    public function studentById(int $id): ?array
    {
        return null;
    }

    public function roomStudents(): array
    {
        return [];
    }

    public function courseDocuments(): array
    {
        return [];
    }

    public function library(): array
    {
        return [];
    }

    public function archives(): array
    {
        return [];
    }

    public function exercises(): array
    {
        return [];
    }

    public function pedagogy(): array
    {
        return [
            'presence' => '0 %',
            'seances' => 0,
            'exercices' => '0/0',
            'progression' => '0 %',
            'difficulte' => [],
            'reguliers' => [],
        ];
    }

    public function notifications(): array
    {
        return [];
    }

    public function lastSessionSummary(): array
    {
        return [
            'matiere' => $this->teacher->matiere ?: '—',
            'salle' => '—',
            'horaire' => '—',
            'presents' => 0,
            'absents' => 0,
            'documents' => 0,
            'exercices' => 0,
            'tableau' => '—',
            'enregistrement' => '—',
        ];
    }
}
