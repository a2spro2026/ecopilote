<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminClassCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_lists_all_subjects_and_school_levels(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.classes.create', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Mathématiques')
            ->assertSee('Informatique')
            ->assertSee('Arabe')
            ->assertSee('Histoire-Géographie')
            ->assertSee('Primaire')
            ->assertSee('Collège')
            ->assertSee('Lycée')
            ->assertSee('Coran');
    }

    public function test_create_form_exposes_students_with_subject_and_level_keys(): void
    {
        $matching = $this->student([
            'nom_complet' => 'Amine College Maths',
            'login' => 'amine.college@esipres.com',
            'niveau_scolaire' => '3e collège',
            'matiere' => 'Mathématiques, SVT',
        ]);
        $this->student([
            'nom_complet' => 'Sara Lycee Anglais',
            'login' => 'sara.lycee@esipres.com',
            'niveau_scolaire' => '2nde',
            'matiere' => 'Anglais',
        ]);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.classes.create', ['embed' => 1]))
            ->assertOk();

        $response->assertSee('Amine College Maths', false);
        $response->assertSee('"niveau_key":"college"', false);
        $response->assertSee('"niveau_key":"lycee"', false);
        $response->assertSee($matching->id);
    }

    public function test_create_form_maps_uppercase_college_label_and_unassigned_level(): void
    {
        $this->student([
            'nom_complet' => 'Nour College Label',
            'login' => 'nour.college@esipres.com',
            'niveau_scolaire' => 'COLLÈGE',
            'matiere' => 'MATHÉMATIQUES',
        ]);
        $this->student([
            'nom_complet' => 'Imane Sans Niveau',
            'login' => 'imane.sans@esipres.com',
            'niveau_scolaire' => 'Non renseigné',
            'matiere' => 'Mathématiques',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.classes.create', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Nour College Label', false)
            ->assertSee('Imane Sans Niveau', false)
            ->assertSee('"niveau_key":"college"', false);
    }

    public function test_store_accepts_a_student_with_matching_subject_even_if_level_is_blank(): void
    {
        $teacher = $this->teacher([
            'matiere' => 'Mathématiques',
            'niveau' => 'college',
        ]);
        $student = $this->student([
            'nom_complet' => 'Imane Sans Niveau',
            'login' => 'imane.sans@esipres.com',
            'niveau_scolaire' => 'Non renseigné',
            'matiere' => 'Mathématiques',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.classes.store'), $this->payload([
                'matiere' => 'Mathématiques',
                'niveau' => 'college',
                'professeur_id' => $teacher->id,
                'eleves' => [$student->id],
            ]))
            ->assertRedirect(route('admin.classes.create'))
            ->assertSessionHas('success')
            ->assertSessionHasNoErrors();
    }

    public function test_store_accepts_a_teacher_of_the_same_subject_even_if_levels_differ(): void
    {
        $teacher = $this->teacher([
            'nom_complet' => 'Nadia Maths Lycee',
            'login' => 'nadia.maths@esipres.com',
            'matiere' => 'Mathématiques',
            'niveau' => 'lycee',
        ]);
        $student = $this->student([
            'nom_complet' => 'Yassine College Maths',
            'login' => 'yassine.college@esipres.com',
            'niveau_scolaire' => '4ème collège',
            'matiere' => 'Mathématiques',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.classes.store'), $this->payload([
                'matiere' => 'Mathématiques',
                'niveau' => 'college',
                'professeur_id' => $teacher->id,
                'eleves' => [$student->id],
            ]))
            ->assertRedirect(route('admin.classes.create'))
            ->assertSessionHas('success')
            ->assertSessionHasNoErrors();
    }

    public function test_store_rejects_a_teacher_of_another_subject(): void
    {
        $teacher = $this->teacher([
            'matiere' => 'Anglais',
            'niveau' => 'college',
        ]);
        $student = $this->student([
            'niveau_scolaire' => '3e collège',
            'matiere' => 'Mathématiques',
        ]);

        $this->actingAs($this->admin())
            ->from(route('admin.classes.create'))
            ->post(route('admin.classes.store'), $this->payload([
                'matiere' => 'Mathématiques',
                'niveau' => 'college',
                'professeur_id' => $teacher->id,
                'eleves' => [$student->id],
            ]))
            ->assertRedirect(route('admin.classes.create'))
            ->assertSessionHasErrors('matiere');
    }

    public function test_store_rejects_a_student_outside_selected_subject_or_level(): void
    {
        $teacher = $this->teacher([
            'matiere' => 'Mathématiques',
            'niveau' => 'college',
        ]);
        $wrongLevel = $this->student([
            'nom_complet' => 'Lina Lycee Maths',
            'login' => 'lina.lycee@esipres.com',
            'niveau_scolaire' => 'Terminale',
            'matiere' => 'Mathématiques',
        ]);

        $this->actingAs($this->admin())
            ->from(route('admin.classes.create'))
            ->post(route('admin.classes.store'), $this->payload([
                'matiere' => 'Mathématiques',
                'niveau' => 'college',
                'professeur_id' => $teacher->id,
                'eleves' => [$wrongLevel->id],
            ]))
            ->assertRedirect(route('admin.classes.create'))
            ->assertSessionHasErrors('eleves');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'numero' => 'CL-0001',
            'matiere' => 'Mathématiques',
            'niveau' => 'college',
            'type' => 'individuelle',
            'statut' => 'active',
            'jours' => ['Lundi'],
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
            'date_debut' => '2026-09-01',
            'sans_date_fin' => '1',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function student(array $attributes = []): Student
    {
        return Student::create(array_merge([
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
        ], $attributes));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function teacher(array $attributes = []): Teacher
    {
        return Teacher::create(array_merge([
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
        ], $attributes));
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }
}
