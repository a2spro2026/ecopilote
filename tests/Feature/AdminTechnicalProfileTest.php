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
                'nom_complet' => 'Yasmine Alaoui',
                'contact' => '0600000000',
                'tuteur_nom' => 'Ahmed Alaoui',
                'contact_tuteur' => '0611111111',
                'matieres' => ['Mathématiques', 'SVT'],
                'niveau_scolaire' => 'college',
                'paiement' => '1200',
                'mode_paiement' => 'virement',
                'periode_paiement' => 'trimestre',
                'login' => 'yasmine.alaoui',
                'access_password' => 'nouveau123',
            ])
            ->assertRedirect(route('admin.students.technical'))
            ->assertSessionHasNoErrors();

        $student->refresh();
        $this->assertSame('AHMED ALAOUI', $student->tuteur_nom);
        $this->assertSame('Mathématiques, SVT', $student->matiere);
        $this->assertSame('yasmine.alaoui@esipres.com', $student->login);
        $this->assertSame('nouveau123', $student->access_password);
        $this->assertSame('trimestre', $student->periode_paiement);
    }

    public function test_superadmin_can_update_a_teacher_technical_profile(): void
    {
        $teacher = Teacher::create([
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

        $this->actingAs($this->admin())
            ->post(route('admin.teachers.technical.store'), [
                'teacher_id' => $teacher->id,
                'nom_complet' => 'Nadia El Amrani',
                'contact' => '0600000000',
                'ville' => 'Casablanca',
                'statut' => 'prive',
                'matieres' => ['Mathématiques', 'Physique-Chimie'],
                'paiement' => 'salaire',
                'paiement_valeur' => '5000',
                'periode_paiement' => 'mois',
                'login' => 'nadia.el.amrani',
                'access_password' => 'professeur123',
            ])
            ->assertRedirect(route('admin.teachers.technical'))
            ->assertSessionHasNoErrors();

        $teacher->refresh();
        $this->assertSame('Mathématiques, Physique-Chimie', $teacher->matiere);
        $this->assertSame('salaire', $teacher->paiement);
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
                'nom_complet' => 'Yasmine Alaoui',
                'contact' => '0600000000',
                'contact_tuteur' => '0611111111',
                'matieres' => ['Mathématiques'],
                'niveau_scolaire' => 'college',
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

    public function test_fiche_eleve_page_shows_the_student_table(): void
    {
        $student = $this->student();
        $student->update([
            'tuteur_nom' => 'Ahmed Alaoui',
            'matiere' => 'Mathématiques, SVT',
            'paiement' => '1200.00',
            'mode_paiement' => 'virement',
            'periode_paiement' => 'trimestre',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.students.technical'))
            ->assertOk()
            ->assertSee('Fiche Élève')
            ->assertSee('Nom Complet')
            ->assertSee('Nom Tuteur')
            ->assertSee('Matières')
            ->assertSee('Ajouter')
            ->assertSee('Importer Photo')
            ->assertSee($student->displayId())
            ->assertSee('Yasmine Alaoui')
            ->assertSee('Ahmed Alaoui')
            ->assertSee('Mathématiques, SVT')
            ->assertDontSee('studentIdOptions');
    }

    public function test_superadmin_can_create_a_student_fiche(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.students.technical.store'), [
                'nom_complet' => 'Karim Bennani',
                'contact' => '0622222222',
                'tuteur_nom' => 'Samira Bennani',
                'contact_tuteur' => '0633333333',
                'matieres' => ['Anglais', 'Français'],
                'niveau_scolaire' => 'lycee',
                'paiement' => '800',
                'mode_paiement' => 'especes',
                'periode_paiement' => 'mois',
                'login' => 'karim.bennani',
                'access_password' => 'eleve456',
            ])
            ->assertRedirect(route('admin.students.technical'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', [
            'nom_complet' => 'KARIM BENNANI',
            'contact' => '0622222222',
            'tuteur_nom' => 'SAMIRA BENNANI',
            'matiere' => 'Anglais, Français',
            'niveau_scolaire' => 'lycee',
            'mode_paiement' => 'especes',
            'login' => 'karim.bennani@esipres.com',
        ]);
    }

    public function test_fiche_professeur_page_shows_the_teacher_table(): void
    {
        $teacher = Teacher::create([
            'nom_complet' => 'Nadia El Amrani',
            'login' => 'nadia.el.amrani@esipres.com',
            'access_password' => 'secret12',
            'contact' => '0600000000',
            'ville' => 'Casablanca',
            'statut' => 'prive',
            'matiere' => 'Mathématiques, SVT',
            'niveau' => 'college',
            'disponibilite' => 'immediat',
            'etat' => Teacher::ETAT_ACTIF,
            'paiement' => 'salaire',
            'paiement_valeur' => '5000.00',
            'periode_paiement' => 'mois',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.teachers.technical'))
            ->assertOk()
            ->assertSee('Fiche Professeur')
            ->assertSee('Nom Complet')
            ->assertSee('Statut')
            ->assertSee('Matière')
            ->assertSee('Ajouter')
            ->assertSee('Importer Photo')
            ->assertSee($teacher->displayId())
            ->assertSee('Nadia El Amrani')
            ->assertSee('Mathématiques, SVT')
            ->assertDontSee('teacherIdOptions');
    }

    public function test_superadmin_can_create_a_teacher_fiche(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.teachers.technical.store'), [
                'nom_complet' => 'Karim Bennani',
                'contact' => '0622222222',
                'ville' => 'Rabat',
                'statut' => 'public',
                'matieres' => ['Anglais', 'Français'],
                'paiement' => 'commission',
                'paiement_valeur' => '800',
                'periode_paiement' => 'trimestre',
                'login' => 'karim.bennani',
                'access_password' => 'professeur456',
            ])
            ->assertRedirect(route('admin.teachers.technical'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('teachers', [
            'nom_complet' => 'KARIM BENNANI',
            'contact' => '0622222222',
            'ville' => 'RABAT',
            'statut' => 'public',
            'matiere' => 'Anglais, Français',
            'paiement' => 'commission',
            'login' => 'karim.bennani@esipres.com',
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
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
