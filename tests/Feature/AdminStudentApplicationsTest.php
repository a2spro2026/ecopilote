<?php

namespace Tests\Feature;

use App\Models\StudentApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStudentApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_demandes_page_shows_table_columns_and_action_icons(): void
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

        $this->actingAs($this->admin())
            ->get(route('admin.page.demandes-eleves', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Date')
            ->assertSee('N° Demande')
            ->assertSee('Nom Complet')
            ->assertSee('Contact')
            ->assertSee('Ville')
            ->assertSee('Niveau')
            ->assertSee('Matière')
            ->assertSee('Type Cour')
            ->assertSee('Actions')
            ->assertSee('DE-0001')
            ->assertSee('Yasmine Alaoui')
            ->assertSee('Casablanca')
            ->assertSee('aria-label="Valider"', false)
            ->assertSee('aria-label="En attente"', false)
            ->assertSee('aria-label="Suspendre"', false)
            ->assertDontSee('Aucune demande pour le moment');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }
}
