<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_workspace_requires_a_student_session(): void
    {
        $this->get('/espace-eleve')
            ->assertRedirect(route('portail.etudiant'));
    }

    public function test_active_student_can_login_with_local_identifier(): void
    {
        $student = $this->student();

        $this->post('/portail-etudiant', [
            'email' => 'yasmine.alaoui',
            'password' => 'eleve123',
        ])->assertRedirect(route('student.dashboard'));

        $this->assertSame($student->id, session('student_id'));
    }

    public function test_suspended_student_cannot_login(): void
    {
        $this->student(['etat' => Student::ETAT_SUSPENDU]);

        $this->post('/portail-etudiant', [
            'email' => 'yasmine.alaoui',
            'password' => 'eleve123',
        ])->assertSessionHasErrors('email');

        $this->assertNull(session('student_id'));
    }

    public function test_authenticated_student_can_open_all_workspace_pages(): void
    {
        $student = $this->student();
        $this->withSession(['student_id' => $student->id]);

        foreach ([
            'student.dashboard',
            'student.classes',
            'student.sessions',
            'student.assignments',
            'student.documents',
            'student.progress',
            'student.archives',
            'student.notifications',
            'student.profile',
            'student.room',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_student_can_submit_an_assignment_file(): void
    {
        $student = $this->student();

        $this->withSession(['student_id' => $student->id])
            ->post(route('student.assignments.submit', 1), [
                'submission' => UploadedFile::fake()->create('devoir.pdf', 250, 'application/pdf'),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');
    }

    public function test_student_room_exposes_a_restricted_writing_toolkit(): void
    {
        $student = $this->student();

        $this->withSession(['student_id' => $student->id])
            ->get(route('student.room'))
            ->assertOk()
            ->assertSee('Demander à écrire')
            ->assertSee('Stylo')
            ->assertSee('Clavier')
            ->assertSee('Gomme')
            ->assertSee('Lignes de cahier')
            ->assertSee('Annuler')
            ->assertSee('Rétablir')
            ->assertDontSee('Effacer tout')
            ->assertDontSee('Nouvelle page');
    }

    public function test_student_writing_tools_are_locked_until_the_teacher_allows_them(): void
    {
        $student = $this->student();

        $response = $this->withSession(['student_id' => $student->id])
            ->get(route('student.room'))
            ->assertOk()
            ->assertSee('Outils verrouillés')
            ->assertSee('Le professeur doit autoriser l’écriture', false);

        $html = $response->getContent();

        $this->assertSame(
            substr_count($html, 'class="student-tool'),
            substr_count($html, 'disabled class="student-tool'),
            'Chaque outil élève doit être désactivé par défaut.'
        );
        $this->assertStringContainsString('data-student-color="#2563eb" disabled', $html);
        $this->assertStringContainsString('id="studentShape" disabled', $html);
    }

    public function test_logout_only_clears_student_session(): void
    {
        $student = $this->student();

        $this->withSession(['student_id' => $student->id, 'teacher_id' => 99])
            ->post(route('student.logout'))
            ->assertRedirect(route('portail.etudiant'));

        $this->assertNull(session('student_id'));
        $this->assertSame(99, session('teacher_id'));
    }

    private function student(array $attributes = []): Student
    {
        return Student::create(array_merge([
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
        ], $attributes));
    }
}
