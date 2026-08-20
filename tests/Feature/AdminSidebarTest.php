<?php

namespace Tests\Feature;

use App\Models\StudentApplication;
use App\Models\TeacherApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSidebarTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_shows_the_reorganized_administration_tree(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Vue générale')
            ->assertSee('Administration')
            ->assertSeeInOrder([
                'Département élèves',
                'Demandes',
                'Fiche élève',
                'Département prof',
                'Candidatures',
                'Fiche prof',
            ])
            ->assertSee('Planning')
            ->assertSeeInOrder([
                'Groupe',
                'Classe',
                'Séance',
                'Calendrier',
            ])
            ->assertSee('Évaluations')
            ->assertSee('Relevés')
            ->assertSee('Utilisateurs');
    }

    public function test_notification_menu_lists_pending_demandes_and_candidatures(): void
    {
        StudentApplication::create([
            'nom_complet' => 'Yasmine Alaoui',
            'contact' => '0600000000',
            'contact_tuteur' => '0611111111',
            'ville' => 'Casablanca',
            'niveau_scolaire' => 'college',
            'matiere' => 'Mathématiques',
            'type_cours' => 'en_groupe',
            'etat' => StudentApplication::ETAT_EN_ATTENTE,
        ]);

        TeacherApplication::create([
            'nom_complet' => 'Nadia El Amrani',
            'contact' => '0600000000',
            'ville' => 'Casablanca',
            'matiere' => 'Mathématiques',
            'niveau' => 'college',
            'statut' => 'prive',
            'disponibilite' => 'immediat',
            'etat' => TeacherApplication::ETAT_EN_ATTENTE,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('toggleNotifMenu', false)
            ->assertSee('notifMenu')
            ->assertSee('Yasmine Alaoui')
            ->assertSee('DE-0001')
            ->assertSee('Nadia El Amrani')
            ->assertSee('CP-0001')
            ->assertSee('Ouvrir le tableau Demandes')
            ->assertSee('Ouvrir le tableau Candidatures')
            ->assertSee(route('admin.page.demandes-eleves'), false)
            ->assertSee(route('admin.page.candidatures-profs'), false);
    }

    public function test_new_placeholder_pages_are_reachable(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.page.etat-apprentissage', ['embed' => 1]))
            ->assertOk()
            ->assertSee('État d’apprentissage')
            ->assertSee('Séances / Mois')
            ->assertDontSee('Contenu à venir');

        $this->actingAs($this->admin())
            ->get(route('admin.page.seances', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Séance')
            ->assertDontSee('Contenu à venir');

        $this->actingAs($this->admin())
            ->get(route('admin.page.calendrier', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Calendrier')
            ->assertDontSee('Contenu à venir');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }
}
