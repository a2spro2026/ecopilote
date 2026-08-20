<?php

namespace Tests\Feature;

use App\Models\TeacherApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTeacherApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidatures_page_shows_table_columns_and_action_icons(): void
    {
        TeacherApplication::create([
            'nom_complet' => 'Nadia El Amrani',
            'contact' => '0600000000',
            'ville' => 'Casablanca',
            'matiere' => 'Mathématiques, Physique-Chimie',
            'niveau' => 'college',
            'statut' => 'prive',
            'disponibilite' => 'immediat',
            'etat' => TeacherApplication::ETAT_EN_ATTENTE,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.page.candidatures-profs', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Date')
            ->assertSee('N° Demande')
            ->assertSee('Nom Complet')
            ->assertSee('Contact')
            ->assertSee('Ville')
            ->assertSee('Niveau')
            ->assertSee('Matière')
            ->assertSee('Statut')
            ->assertSee('Actions')
            ->assertSee('CP-0001')
            ->assertSee('Nadia El Amrani')
            ->assertSee('Casablanca')
            ->assertSee('Collège')
            ->assertSee('Privé')
            ->assertSee('aria-label="Valider"', false)
            ->assertSee('aria-label="En attente"', false)
            ->assertSee('aria-label="Suspendre"', false)
            ->assertDontSee('Aucune candidature pour le moment');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }
}
