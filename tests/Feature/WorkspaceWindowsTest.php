<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceWindowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_page_renders_the_mdi_desktop(): void
    {
        $this->withSession(['student_id' => $this->student()->id])
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('mdiDesktop')
            ->assertSee('mdiWindowTemplate');
    }

    public function test_embedded_page_renders_only_its_content(): void
    {
        $this->withSession(['student_id' => $this->student()->id])
            ->get(route('student.classes', ['embed' => 1]))
            ->assertOk()
            ->assertDontSee('mdiDesktop')
            ->assertDontSee('studentSidebar')
            ->assertSee('ecopilote:window-navigate');
    }

    public function test_admin_pages_open_in_mdi_windows_and_support_embedding(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.students.technical'))
            ->assertOk()
            ->assertSee('mdiDesktop');

        $this->actingAs($admin)
            ->get(route('admin.students.technical', ['embed' => 1]))
            ->assertOk()
            ->assertDontSee('adminSidebar');
    }

    public function test_student_login_clears_leftover_admin_session(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        $student = $this->student();

        $this->actingAs($admin)
            ->post(route('portail.etudiant.login'), [
                'email' => 'yasmine.alaoui',
                'password' => 'eleve123',
            ])
            ->assertRedirect(route('student.dashboard'));

        $this->assertGuest();
        $this->assertSame($student->id, session('student_id'));

        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('/espace-eleve')
            ->assertDontSee('Espace Administration');
    }

    public function test_admin_login_clears_student_session(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_SUPERADMIN,
            'email' => 'hanan@esipres.com',
        ]);
        $student = $this->student();

        $this->withSession(['student_id' => $student->id])
            ->post(route('admin.login.attempt'), [
                'email' => 'hanan',
                'password' => 'password',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session('student_id'));
    }

    public function test_student_workspace_drops_leftover_admin_auth(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        $student = $this->student();

        $this->actingAs($admin)
            ->withSession(['student_id' => $student->id])
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertDontSee('Espace Administration');

        $this->assertGuest();
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_teacher_login_clears_leftover_admin_session(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        $teacher = Teacher::query()->create([
            'nom_complet' => 'Nadia El Amrani',
            'login' => 'nadia.el.amrani@esipres.com',
            'access_password' => 'secret12',
            'contact' => '0600000000',
            'ville' => 'Casablanca',
            'statut' => 'prive',
            'matiere' => 'Mathématiques',
            'niveau' => 'college',
            'disponibilite' => 'immediat',
            'etat' => Teacher::ETAT_ACTIF,
        ]);

        $this->actingAs($admin)
            ->post(route('portail.profs.login'), [
                'login' => 'nadia.el.amrani',
                'password' => 'secret12',
            ])
            ->assertRedirect(route('teacher.bureau'));

        $this->assertGuest();
        $this->assertSame($teacher->id, session('teacher_id'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('teacher.bureau'))
            ->assertOk()
            ->assertDontSee('Espace Administration');
    }

    private function student(): Student
    {
        return Student::create([
            'nom_complet' => 'Yasmine Alaoui',
            'login' => 'yasmine.alaoui@esipres.com',
            'access_password' => 'eleve123',
            'contact' => '0600000000',
            'contact_tuteur' => '0611111111',
            'ville' => 'Casablanca',
            'niveau_scolaire' => '3e collège',
            'matiere' => 'Mathématiques',
            'type_cours' => 'en_groupe',
            'etat' => Student::ETAT_ACTIF,
        ]);
    }
}
