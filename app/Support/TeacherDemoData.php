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
            'matiere' => $this->teacher->matiere ?: 'Mathématiques',
            'code' => $this->teacher->displayId(),
            'etat' => 'validé',
        ];
    }

    public function kpis(): array
    {
        $groupes = count($this->classes());
        $eleves = count($this->students());
        $seancesMois = count($this->sessions('mois'));
        $revenu = $this->teacher->paiement_valeur
            ? number_format((float) $this->teacher->paiement_valeur, 0, ',', ' ')
            : '12 400';
        $solde = '3 850';

        return [
            ['key' => 'groupes', 'label' => 'Mes Groupes', 'value' => (string) $groupes, 'hint' => $groupes.' groupes actifs', 'up' => true, 'tone' => 'emerald', 'icon' => 'groups', 'suffix' => ''],
            ['key' => 'eleves', 'label' => 'Mes élèves', 'value' => (string) $eleves, 'hint' => 'Suivi pédagogique', 'up' => true, 'tone' => 'blue', 'icon' => 'users', 'suffix' => ''],
            ['key' => 'seances', 'label' => 'Séances / Mois', 'value' => (string) $seancesMois, 'hint' => 'Ce mois-ci', 'up' => true, 'tone' => 'indigo', 'icon' => 'calendar', 'suffix' => ''],
            ['key' => 'revenu', 'label' => 'Total Revenue / Mois', 'value' => $revenu, 'hint' => 'MAD ce mois', 'up' => true, 'tone' => 'violet', 'icon' => 'money', 'suffix' => 'MAD'],
            ['key' => 'solde', 'label' => 'Solde', 'value' => $solde, 'hint' => 'Disponible', 'up' => true, 'tone' => 'amber', 'icon' => 'wallet', 'suffix' => 'MAD'],
        ];
    }

    public function nextSession(): array
    {
        return [
            'id' => 'salle-01',
            'classe_id' => 1,
            'matiere' => 'Mathématiques',
            'salle' => 'Salle 01',
            'debut' => '18:00',
            'fin' => '19:30',
            'effectif' => 12,
            'statut' => 'prete',
            'statut_label' => 'Prête à commencer',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function classes(): array
    {
        return [
            [
                'id' => 1,
                'salle' => 'Salle 01',
                'matiere' => 'Mathématiques',
                'niveau' => '3ème année collège',
                'effectif' => 12,
                'prochaine' => 'Aujourd’hui — 18:00',
                'type' => 'Groupe',
            ],
            [
                'id' => 2,
                'salle' => 'Salle 04',
                'matiere' => 'Mathématiques',
                'niveau' => '2ème année collège',
                'effectif' => 10,
                'prochaine' => 'Demain — 17:00',
                'type' => 'Groupe',
            ],
            [
                'id' => 3,
                'salle' => 'Salle 07',
                'matiere' => 'Mathématiques',
                'niveau' => '1ère année lycée',
                'effectif' => 8,
                'prochaine' => 'Samedi — 10:00',
                'type' => 'Groupe',
            ],
        ];
    }

    public function classById(int $id): ?array
    {
        return collect($this->classes())->firstWhere('id', $id);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function sessions(?string $filter = 'toutes'): array
    {
        $items = [
            ['id' => 1, 'heure' => '18:00 – 19:30', 'date' => 'Aujourd’hui', 'classe' => 'Salle 01', 'matiere' => 'Mathématiques', 'eleves' => 12, 'professeur' => $this->teacher->nom_complet, 'statut' => 'a_venir', 'periode' => 'aujourdhui', 'salle_id' => 'salle-01'],
            ['id' => 2, 'heure' => '10:00 – 11:30', 'date' => 'Aujourd’hui', 'classe' => 'Salle 07', 'matiere' => 'Mathématiques', 'eleves' => 8, 'professeur' => $this->teacher->nom_complet, 'statut' => 'terminee', 'periode' => 'aujourdhui', 'salle_id' => null],
            ['id' => 3, 'heure' => '17:00 – 18:00', 'date' => 'Demain', 'classe' => 'Salle 04', 'matiere' => 'Mathématiques', 'eleves' => 10, 'professeur' => $this->teacher->nom_complet, 'statut' => 'a_venir', 'periode' => 'semaine', 'salle_id' => 'salle-04'],
            ['id' => 4, 'heure' => '16:00 – 17:30', 'date' => 'Lundi', 'classe' => 'Salle 01', 'matiere' => 'Mathématiques', 'eleves' => 12, 'professeur' => $this->teacher->nom_complet, 'statut' => 'en_direct', 'periode' => 'semaine', 'salle_id' => 'salle-01'],
            ['id' => 5, 'heure' => '09:00 – 10:30', 'date' => '12 août', 'classe' => 'Salle 04', 'matiere' => 'Mathématiques', 'eleves' => 10, 'professeur' => $this->teacher->nom_complet, 'statut' => 'terminee', 'periode' => 'mois', 'salle_id' => null],
            ['id' => 6, 'heure' => '18:00 – 19:30', 'date' => '5 août', 'classe' => 'Salle 01', 'matiere' => 'Mathématiques', 'eleves' => 12, 'professeur' => $this->teacher->nom_complet, 'statut' => 'annulee', 'periode' => 'mois', 'salle_id' => null],
        ];

        return match ($filter) {
            'aujourdhui' => array_values(array_filter($items, fn ($s) => $s['periode'] === 'aujourdhui')),
            'semaine' => array_values(array_filter($items, fn ($s) => in_array($s['periode'], ['aujourdhui', 'semaine'], true))),
            'mois' => array_values(array_filter($items, fn ($s) => in_array($s['periode'], ['aujourdhui', 'semaine', 'mois'], true))),
            default => $items,
        };
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function sessionsToday(): array
    {
        return $this->sessions('aujourdhui');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function students(?int $classeId = null): array
    {
        $all = [
            ['id' => 1, 'nom' => 'Ahmed Bennani', 'niveau' => '3ème collège', 'classe' => 'Salle 01', 'classe_id' => 1, 'presence' => '92 %', 'seances' => 18, 'progression' => 78, 'exercices' => '14/16', 'activite' => 'Il y a 2 h', 'statut' => 'present'],
            ['id' => 2, 'nom' => 'Sara El Fassi', 'niveau' => '3ème collège', 'classe' => 'Salle 01', 'classe_id' => 1, 'presence' => '100 %', 'seances' => 18, 'progression' => 91, 'exercices' => '16/16', 'activite' => 'En ligne', 'statut' => 'en_ligne'],
            ['id' => 3, 'nom' => 'Youssef Tazi', 'niveau' => '3ème collège', 'classe' => 'Salle 01', 'classe_id' => 1, 'presence' => '78 %', 'seances' => 14, 'progression' => 54, 'exercices' => '9/16', 'activite' => 'Hier', 'statut' => 'present'],
            ['id' => 4, 'nom' => 'Amine Kabbaj', 'niveau' => '3ème collège', 'classe' => 'Salle 01', 'classe_id' => 1, 'presence' => '61 %', 'seances' => 11, 'progression' => 42, 'exercices' => '6/16', 'activite' => 'Il y a 4 j', 'statut' => 'absent'],
            ['id' => 5, 'nom' => 'Salma Chraibi', 'niveau' => '3ème collège', 'classe' => 'Salle 01', 'classe_id' => 1, 'presence' => '88 %', 'seances' => 16, 'progression' => 71, 'exercices' => '12/16', 'activite' => 'Hier', 'statut' => 'deconnecte'],
            ['id' => 6, 'nom' => 'Imane Alaoui', 'niveau' => '2ème collège', 'classe' => 'Salle 04', 'classe_id' => 2, 'presence' => '95 %', 'seances' => 15, 'progression' => 84, 'exercices' => '11/12', 'activite' => 'Aujourd’hui', 'statut' => 'present'],
            ['id' => 7, 'nom' => 'Omar Lahlou', 'niveau' => '2ème collège', 'classe' => 'Salle 04', 'classe_id' => 2, 'presence' => '70 %', 'seances' => 12, 'progression' => 48, 'exercices' => '7/12', 'activite' => 'Il y a 3 j', 'statut' => 'absent'],
            ['id' => 8, 'nom' => 'Nour Benjelloun', 'niveau' => '2ème collège', 'classe' => 'Salle 04', 'classe_id' => 2, 'presence' => '100 %', 'seances' => 15, 'progression' => 89, 'exercices' => '12/12', 'activite' => 'En ligne', 'statut' => 'en_ligne'],
            ['id' => 9, 'nom' => 'Mehdi Serhani', 'niveau' => '1ère lycée', 'classe' => 'Salle 07', 'classe_id' => 3, 'presence' => '83 %', 'seances' => 10, 'progression' => 67, 'exercices' => '8/10', 'activite' => 'Hier', 'statut' => 'present'],
            ['id' => 10, 'nom' => 'Rania Filali', 'niveau' => '1ère lycée', 'classe' => 'Salle 07', 'classe_id' => 3, 'presence' => '90 %', 'seances' => 10, 'progression' => 82, 'exercices' => '9/10', 'activite' => 'Aujourd’hui', 'statut' => 'present'],
        ];

        if ($classeId) {
            return array_values(array_filter($all, fn ($s) => $s['classe_id'] === $classeId));
        }

        return $all;
    }

    public function studentById(int $id): ?array
    {
        return collect($this->students())->firstWhere('id', $id);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function roomStudents(): array
    {
        return [
            ['nom' => 'Ahmed Bennani', 'etat' => 'en_ligne'],
            ['nom' => 'Sara El Fassi', 'etat' => 'en_ligne'],
            ['nom' => 'Youssef Tazi', 'etat' => 'present'],
            ['nom' => 'Amine Kabbaj', 'etat' => 'absent'],
            ['nom' => 'Salma Chraibi', 'etat' => 'deconnecte'],
            ['nom' => 'Imane Alaoui', 'etat' => 'en_ligne'],
            ['nom' => 'Omar Lahlou', 'etat' => 'present'],
            ['nom' => 'Nour Benjelloun', 'etat' => 'en_ligne'],
            ['nom' => 'Mehdi Serhani', 'etat' => 'present'],
            ['nom' => 'Rania Filali', 'etat' => 'en_ligne'],
            ['nom' => 'Hiba Toumi', 'etat' => 'present'],
            ['nom' => 'Anas Ouazzani', 'etat' => 'absent'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function courseDocuments(): array
    {
        return [
            ['id' => 1, 'nom' => 'Théorème de Pythagore.pdf', 'type' => 'PDF', 'section' => 'Cours'],
            ['id' => 2, 'nom' => 'Figure triangle.png', 'type' => 'Image', 'section' => 'Supports'],
            ['id' => 3, 'nom' => 'Exercices 12-18.pdf', 'type' => 'Exercice', 'section' => 'Exercices'],
            ['id' => 4, 'nom' => 'Diapo géométrie.pptx', 'type' => 'Présentation', 'section' => 'Supports'],
            ['id' => 5, 'nom' => 'Rappel vidéo.mp4', 'type' => 'Vidéo', 'section' => 'Vidéos'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function library(): array
    {
        return [
            ['id' => 1, 'nom' => 'Cours — Géométrie plane', 'section' => 'Cours', 'classe' => 'Salle 01', 'date' => '10 août 2026'],
            ['id' => 2, 'nom' => 'Exercices — Équations', 'section' => 'Exercices', 'classe' => 'Salle 04', 'date' => '8 août 2026'],
            ['id' => 3, 'nom' => 'Devoir maison n°4', 'section' => 'Devoirs', 'classe' => 'Salle 07', 'date' => '6 août 2026'],
            ['id' => 4, 'nom' => 'Corrigé devoir n°3', 'section' => 'Corrections', 'classe' => 'Salle 01', 'date' => '4 août 2026'],
            ['id' => 5, 'nom' => 'Enregistrement séance 12/08', 'section' => 'Vidéos', 'classe' => 'Salle 01', 'date' => '12 août 2026'],
            ['id' => 6, 'nom' => 'Fiche formules', 'section' => 'Documents', 'classe' => 'Toutes', 'date' => '1 août 2026'],
            ['id' => 7, 'nom' => 'Support tableau blanc', 'section' => 'Supports', 'classe' => 'Salle 04', 'date' => '28 juillet 2026'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function archives(): array
    {
        return [
            ['id' => 1, 'titre' => 'Séance 12 août — Salle 01', 'type' => 'Cours', 'matiere' => 'Mathématiques', 'classe' => 'Salle 01', 'date' => '12 août 2026', 'enseignant' => $this->teacher->nom_complet],
            ['id' => 2, 'titre' => 'Tableau sauvegardé — Pythagore', 'type' => 'Tableau', 'matiere' => 'Mathématiques', 'classe' => 'Salle 01', 'date' => '12 août 2026', 'enseignant' => $this->teacher->nom_complet],
            ['id' => 3, 'titre' => 'Enregistrement 10:00', 'type' => 'Vidéo', 'matiere' => 'Mathématiques', 'classe' => 'Salle 07', 'date' => '14 août 2026', 'enseignant' => $this->teacher->nom_complet],
            ['id' => 4, 'titre' => 'Devoir n°3 — copies', 'type' => 'Exercice', 'matiere' => 'Mathématiques', 'classe' => 'Salle 04', 'date' => '2 août 2026', 'enseignant' => $this->teacher->nom_complet],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function exercises(): array
    {
        return [
            ['id' => 1, 'titre' => 'Équations du premier degré', 'classe' => 'Salle 04', 'limite' => '18 août 2026', 'statut' => 'a_corriger', 'remis' => '8/10'],
            ['id' => 2, 'titre' => 'Théorème de Pythagore', 'classe' => 'Salle 01', 'limite' => '16 août 2026', 'statut' => 'corrige', 'remis' => '11/12'],
            ['id' => 3, 'titre' => 'Fonctions linéaires', 'classe' => 'Salle 07', 'limite' => '20 août 2026', 'statut' => 'non_remis', 'remis' => '3/8'],
        ];
    }

    public function pedagogy(): array
    {
        return [
            'presence' => '86 %',
            'seances' => 24,
            'exercices' => '41/48',
            'progression' => '71 %',
            'difficulte' => ['Amine Kabbaj', 'Omar Lahlou', 'Youssef Tazi'],
            'reguliers' => ['Sara El Fassi', 'Nour Benjelloun', 'Imane Alaoui'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function notifications(): array
    {
        return [
            ['title' => 'Séance dans 40 min', 'text' => 'Mathématiques — Salle 01 à 18:00', 'time' => 'Maintenant'],
            ['title' => '3 exercices à corriger', 'text' => 'Classe 2ème année collège', 'time' => 'Il y a 1 h'],
            ['title' => 'Amine Kabbaj absent', 'text' => '2 absences consécutives', 'time' => 'Hier'],
        ];
    }

    public function lastSessionSummary(): array
    {
        return [
            'matiere' => 'Mathématiques',
            'salle' => 'Salle 01',
            'horaire' => '18:00 — 19:30',
            'presents' => 10,
            'absents' => 2,
            'documents' => 4,
            'exercices' => 2,
            'tableau' => 'sauvegardé',
            'enregistrement' => 'disponible',
        ];
    }
}
