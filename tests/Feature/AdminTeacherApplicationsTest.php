<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\TeacherApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTeacherApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidatures_page_shows_login_password_columns(): void
    {
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
            ->get(route('admin.page.candidatures-profs', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Login')
            ->assertSee('Mot de passe')
            ->assertSee('@esipres.com')
            ->assertSee('nadia.el.amrani')
            ->assertSee('aria-label="Valider"', false);
    }

    public function test_admin_can_validate_candidature_with_login_and_password(): void
    {
        $application = TeacherApplication::create([
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
            ->post(route('admin.teachers.applications.validate', $application), [
                'login' => 'nadia.el.amrani',
                'access_password' => 'secret12',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $application->refresh();
        $this->assertSame(TeacherApplication::ETAT_VALIDEE, $application->etat);
        $this->assertSame('nadia.el.amrani@esipres.com', $application->login);
        $this->assertSame('secret12', $application->access_password);

        $teacher = Teacher::query()->first();
        $this->assertNotNull($teacher);
        $this->assertSame('nadia.el.amrani@esipres.com', $teacher->login);
        $this->assertSame('secret12', $teacher->access_password);
        $this->assertSame(Teacher::ETAT_ACTIF, $teacher->etat);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }
}
