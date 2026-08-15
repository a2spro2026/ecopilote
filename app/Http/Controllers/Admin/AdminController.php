<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
                ['label' => 'Élèves actifs', 'value' => '248', 'hint' => '+12 ce mois', 'up' => true, 'tone' => 'blue', 'icon' => 'users'],
                ['label' => 'Professeurs actifs', 'value' => '64', 'hint' => '+3 validés', 'up' => true, 'tone' => 'emerald', 'icon' => 'teacher'],
                ['label' => "Séances aujourd'hui", 'value' => '18', 'hint' => '6 restantes', 'up' => true, 'tone' => 'indigo', 'icon' => 'calendar'],
                ['label' => 'Séances en direct', 'value' => '3', 'hint' => 'En cours', 'up' => true, 'tone' => 'green', 'icon' => 'live'],
                ['label' => 'Demandes en attente', 'value' => '7', 'hint' => 'À traiter', 'up' => false, 'tone' => 'amber', 'icon' => 'inbox'],
                ['label' => 'Revenus du mois', 'value' => '86 400', 'hint' => 'MAD · +8%', 'up' => true, 'tone' => 'violet', 'icon' => 'money'],
            ],

            'sessions_today' => [
                ['matiere' => 'Mathématiques', 'prof' => 'Mme Alami', 'cible' => 'Yassine B.', 'niveau' => '2nde', 'debut' => '09:00', 'fin' => '10:00', 'duree' => '1h', 'type' => 'individuelle', 'statut' => 'active'],
                ['matiere' => 'Physique-Chimie', 'prof' => 'M. Benali', 'cible' => 'Groupe Terminale S', 'niveau' => 'Terminale', 'debut' => '10:15', 'fin' => '11:45', 'duree' => '1h30', 'type' => 'groupe', 'statut' => 'active'],
                ['matiere' => 'Anglais', 'prof' => 'Mme Carter', 'cible' => 'Sara M.', 'niveau' => '3ème', 'debut' => '11:00', 'fin' => '12:00', 'duree' => '1h', 'type' => 'individuelle', 'statut' => 'programmee'],
                ['matiere' => 'Français', 'prof' => 'M. Idrissi', 'cible' => 'Groupe 1ère', 'niveau' => '1ère', 'debut' => '14:00', 'fin' => '15:00', 'duree' => '1h', 'type' => 'groupe', 'statut' => 'programmee'],
                ['matiere' => 'SVT', 'prof' => 'Mme Zahra', 'cible' => 'Amine K.', 'niveau' => '1ère', 'debut' => '15:30', 'fin' => '16:30', 'duree' => '1h', 'type' => 'individuelle', 'statut' => 'annulee'],
                ['matiere' => 'Histoire-Géo', 'prof' => 'M. Tazi', 'cible' => 'Nour L.', 'niveau' => '4ème', 'debut' => '08:00', 'fin' => '09:00', 'duree' => '1h', 'type' => 'individuelle', 'statut' => 'terminee'],
            ],

            'activity' => [
                ['tone' => 'green', 'title' => 'Séance en direct', 'text' => 'Mathématiques · Mme Alami · Yassine B.', 'time' => 'Maintenant'],
                ['tone' => 'green', 'title' => 'Séance en direct', 'text' => 'Physique · M. Benali · Groupe Terminale S', 'time' => 'Maintenant'],
                ['tone' => 'amber', 'title' => 'Bientôt', 'text' => 'Anglais · Mme Carter · 11:00', 'time' => 'Dans 25 min'],
                ['tone' => 'red', 'title' => 'Séance annulée', 'text' => 'SVT · Mme Zahra · Amine K.', 'time' => 'Il y a 10 min'],
                ['tone' => 'blue', 'title' => 'Nouvelle demande', 'text' => 'Inscription · Collège · Mathématiques', 'time' => 'Il y a 18 min'],
                ['tone' => 'violet', 'title' => 'Nouveau professeur', 'text' => 'Candidature validable · M. Karim (Anglais)', 'time' => 'Il y a 42 min'],
                ['tone' => 'blue', 'title' => 'Nouvelle inscription', 'text' => 'Parent · famille El Amrani', 'time' => 'Il y a 1 h'],
            ],

            'week_days' => ['Lun 11', 'Mar 12', 'Mer 13', 'Jeu 14', 'Ven 15', 'Sam 16', 'Dim 17'],
            'week_slots' => ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00'],
            'week_events' => [
                // dayIndex, slotIndex, label, type, statut
                ['d' => 0, 's' => 0, 'label' => 'Math · Yassine', 'type' => 'individuelle', 'statut' => 'terminee'],
                ['d' => 0, 's' => 3, 'label' => 'Anglais · Groupe', 'type' => 'groupe', 'statut' => 'programmee'],
                ['d' => 1, 's' => 1, 'label' => 'Physique · Direct', 'type' => 'groupe', 'statut' => 'active'],
                ['d' => 2, 's' => 2, 'label' => 'Français · Sara', 'type' => 'individuelle', 'statut' => 'programmee'],
                ['d' => 3, 's' => 0, 'label' => 'SVT · Amine', 'type' => 'individuelle', 'statut' => 'annulee'],
                ['d' => 3, 's' => 4, 'label' => 'Math · Groupe', 'type' => 'groupe', 'statut' => 'programmee'],
                ['d' => 4, 's' => 1, 'label' => 'Histoire · Nour', 'type' => 'individuelle', 'statut' => 'programmee'],
                ['d' => 5, 's' => 0, 'label' => 'Anglais · Direct', 'type' => 'individuelle', 'statut' => 'active'],
            ],

            'archives' => [
                ['kind' => 'Vidéo', 'title' => 'Séance Mathématiques — Yassine B.', 'meta' => 'Mme Alami · 2nde · 10 Août 2026', 'tone' => 'violet'],
                ['kind' => 'Document', 'title' => 'Fiche exercices Physique', 'meta' => 'M. Benali · Terminale · 09 Août 2026', 'tone' => 'blue'],
                ['kind' => 'Cours', 'title' => 'Support Anglais — Present Perfect', 'meta' => 'Mme Carter · 3ème · 08 Août 2026', 'tone' => 'emerald'],
                ['kind' => 'Exercice', 'title' => 'Devoir SVT — Digestion', 'meta' => 'Mme Zahra · 1ère · 07 Août 2026', 'tone' => 'amber'],
                ['kind' => 'Enregistrement', 'title' => 'Replay Français — Analyse littéraire', 'meta' => 'M. Idrissi · 1ère · 06 Août 2026', 'tone' => 'rose'],
                ['kind' => 'Vidéo', 'title' => 'Séance Histoire — La Révolution', 'meta' => 'M. Tazi · 4ème · 05 Août 2026', 'tone' => 'violet'],
            ],
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
