<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTechnicalProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_update_a_student_technical_profile(): void
    {
        $student = $this->student();

        $this->actingAs($this->admin())
            ->post(route('admin.students.technical.store'), [
                'student_id' => $student->id,
                'tuteur_nom' => 'Ahmed Alaoui',
                'contact_tuteur' => '0611111111',
                'matieres' => ['Mathématiques', 'SVT'],
                'paiement' => '1200',
                'mode_paiement' => 'virement',
                'periode_paiement' => 'trimestre',
                'login' => 'yasmine.alaoui',
                'access_password' => 'nouveau123',
            ])
            ->assertRedirect(route('admin.students.technical'))
            ->assertSessionHasNoErrors();

        $student->refresh();
        $this->assertSame('Ahmed Alaoui', $student->tuteur_nom);
        $this->assertSame('Mathématiques, SVT', $student->matiere);
        $this->assertSame('yasmine.alaoui@ecopilote.ma', $student->login);
        $this->assertSame('nouveau123', $student->access_password);
        $this->assertSame('trimestre', $student->periode_paiement);
    }

    public function test_superadmin_can_update_a_teacher_technical_profile(): void
    {
        $teacher = Teacher::create([
            'nom_complet' => 'Nadia El Amrani',
            'login' => 'nadia.el.amrani@ecopilote.ma',
            'access_password' => 'secret12',
            'contact' => '0600000000',
            'ville' => 'Casablanca',
            'statut' => 'prive',
            'matiere' => 'Mathématiques',
            'niveau' => 'college',
            'disponibilite' => 'immediat',
            'etat' => Teacher::ETAT_ACTIF,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.teachers.technical.store'), [
                'teacher_id' => $teacher->id,
                'matieres' => ['Mathématiques', 'Physique-Chimie'],
                'paiement_valeur' => '5000',
                'type_paiement' => 'vir',
                'periode_paiement' => 'mois',
                'login' => 'nadia.el.amrani',
                'access_password' => 'professeur123',
            ])
            ->assertRedirect(route('admin.teachers.technical'))
            ->assertSessionHasNoErrors();

        $teacher->refresh();
        $this->assertSame('Mathématiques, Physique-Chimie', $teacher->matiere);
        $this->assertSame(5000.0, (float) $teacher->paiement_valeur);
        $this->assertSame('professeur123', $teacher->access_password);
        $this->assertSame('mois', $teacher->periode_paiement);
    }

    public function test_uploaded_student_photo_is_stored_and_served_from_the_public_disk(): void
    {
        Storage::fake('public');
        $student = $this->student();

        $this->actingAs($this->admin())
            ->post(route('admin.students.technical.store'), [
                'student_id' => $student->id,
                'contact_tuteur' => '0611111111',
                'matieres' => ['Mathématiques'],
                'paiement' => '1200',
                'mode_paiement' => 'virement',
                'periode_paiement' => 'mois',
                'login' => 'yasmine.alaoui',
                'access_password' => 'nouveau123',
                'photo' => UploadedFile::fake()->create('portrait.jpg', 120, 'image/jpeg'),
            ])
            ->assertRedirect(route('admin.students.technical'))
            ->assertSessionHasNoErrors();

        $student->refresh();
        $this->assertNotNull($student->photo_path);
        Storage::disk('public')->assertExists($student->photo_path);
        $this->assertStringContainsString('/storage/profiles/students/', $student->photo_url);
        $this->assertStringEndsWith('.jpg', $student->photo_url);
    }

    public function test_photo_url_is_null_when_the_file_is_missing(): void
    {
        Storage::fake('public');
        $student = $this->student();
        $student->update(['photo_path' => 'profiles/students/disparue.jpg']);

        $this->assertNull($student->fresh()->photo_url);
    }

    public function test_technical_form_lists_students_by_id_and_by_name(): void
    {
        $student = $this->student();

        $this->actingAs($this->admin())
            ->get(route('admin.students.technical'))
            ->assertOk()
            ->assertSee('studentIdOptions')
            ->assertSee('studentNameOptions')
            ->assertSee($student->displayId())
            ->assertSee('Yasmine Alaoui');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
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
