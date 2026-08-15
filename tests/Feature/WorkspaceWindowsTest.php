<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceWindowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_page_renders_the_window_stack(): void
    {
        $this->withSession(['student_id' => $this->student()->id])
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('workspaceStack')
            ->assertSee('workspaceWindowTemplate');
    }

    public function test_embedded_page_renders_only_its_content(): void
    {
        $this->withSession(['student_id' => $this->student()->id])
            ->get(route('student.classes', ['embed' => 1]))
            ->assertOk()
            ->assertDontSee('workspaceStack')
            ->assertDontSee('studentSidebar')
            ->assertSee('ecopilote:window-navigate');
    }

    public function test_admin_pages_open_in_windows_and_support_embedding(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.students.technical'))
            ->assertOk()
            ->assertSee('workspaceStack');

        $this->actingAs($admin)
            ->get(route('admin.students.technical', ['embed' => 1]))
            ->assertOk()
            ->assertDontSee('adminSidebar');
    }

    private function student(): Student
    {
        return Student::create([
            'nom_complet' => 'Yasmine Alaoui',
            'login' => 'yasmine.alaoui@ecopilote.ma',
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
