<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLearningStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_learning_status_table_lists_students_with_abbreviated_subjects(): void
    {
        $student = Student::create([
            'nom_complet' => 'Yasmine Alaoui',
            'login' => 'yasmine.alaoui@esipres.com',
            'access_password' => 'eleve123',
            'contact' => '0600000000',
            'contact_tuteur' => '0611111111',
            'ville' => 'Casablanca',
            'niveau_scolaire' => 'college',
            'matiere' => 'Mathématiques, Anglais',
            'type_cours' => 'en_groupe',
            'etat' => Student::ETAT_ACTIF,
        ]);

        Student::create([
            'nom_complet' => 'Karim Suspendu',
            'login' => 'karim.suspendu@esipres.com',
            'access_password' => 'eleve123',
            'contact' => '0600000001',
            'contact_tuteur' => '0611111112',
            'ville' => 'Rabat',
            'niveau_scolaire' => 'lycee',
            'matiere' => 'SVT',
            'type_cours' => 'en_groupe',
            'etat' => Student::ETAT_SUSPENDU,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.page.etat-apprentissage', ['embed' => 1]))
            ->assertOk()
            ->assertSee('État d’apprentissage')
            ->assertSee('Mois')
            ->assertSee('Nom élève')
            ->assertSee('ID')
            ->assertSee('Nom Complet')
            ->assertSee('Matière')
            ->assertSee('Séances / Mois')
            ->assertSee('Nom Prof')
            ->assertSee('Classe')
            ->assertSee('Jrs/Cour')
            ->assertSee($student->displayId())
            ->assertSee('Yasmine Alaoui')
            ->assertSee('Math')
            ->assertSee('Ang')
            ->assertDontSee('Karim Suspendu')
            ->assertDontSee('Contenu à venir');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }
}
