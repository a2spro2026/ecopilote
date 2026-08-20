<?php

namespace Tests\Feature;

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
