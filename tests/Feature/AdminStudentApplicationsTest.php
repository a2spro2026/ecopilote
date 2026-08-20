<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudentApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStudentApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_demandes_page_shows_login_password_columns(): void
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
            ->assertSee('Login')
            ->assertSee('Mot de passe')
            ->assertSee('@esipres.com')
            ->assertSee('yasmine.alaoui')
            ->assertSee('aria-label="Valider"', false);
    }

    public function test_admin_can_validate_demande_with_login_and_password(): void
    {
        $application = StudentApplication::create([
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
            ->post(route('admin.students.applications.validate', $application), [
                'login' => 'yasmine.alaoui',
                'access_password' => 'eleve123',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $application->refresh();
        $this->assertSame(StudentApplication::ETAT_VALIDEE, $application->etat);
        $this->assertSame('yasmine.alaoui@esipres.com', $application->login);
        $this->assertSame('eleve123', $application->access_password);

        $student = Student::query()->first();
        $this->assertNotNull($student);
        $this->assertSame('yasmine.alaoui@esipres.com', $student->login);
        $this->assertSame('eleve123', $student->access_password);
        $this->assertSame(Student::ETAT_ACTIF, $student->etat);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }
}
