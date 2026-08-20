<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_page_shows_the_table_and_add_form(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.page.groupes', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Groupe')
            ->assertSee('Réf/G')
            ->assertSee('Matière')
            ->assertSee('Niveau')
            ->assertSee('Nom Prof')
            ->assertSee('Effectif')
            ->assertSee('Revenus')
            ->assertSee('Ajouter')
            ->assertSee('Valider')
            ->assertSee('GR-0001')
            ->assertDontSee('Contenu à venir');
    }

    public function test_superadmin_can_create_a_group_from_matching_inscriptions(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $this->student([
            'nom_complet' => 'Sara Anglais',
            'login' => 'sara.anglais@esipres.com',
            'matiere' => 'Anglais',
            'niveau_scolaire' => 'lycee',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.groups.store'), [
                'matiere' => 'Mathématiques',
                'niveau' => 'college',
                'teacher_id' => $teacher->id,
                'eleves' => [$student->id],
            ])
            ->assertRedirect(route('admin.page.groupes'))
            ->assertSessionHasNoErrors();

        $group = StudyGroup::query()->first();
        $this->assertNotNull($group);
        $this->assertSame('GR-0001', $group->displayId());
        $this->assertSame('Mathématiques', $group->matiere);
        $this->assertSame('college', $group->niveau);
        $this->assertTrue($group->students->contains($student));
        $this->assertSame(1200.0, $group->revenue());

        $this->actingAs($this->admin())
            ->get(route('admin.page.groupes', ['embed' => 1]))
            ->assertOk()
            ->assertSee('GR-0001')
            ->assertSee('Math')
            ->assertSee('Nadia El Amrani')
            ->assertSee('1200.00');
    }

    public function test_group_revenue_sums_each_selected_student_paiement(): void
    {
        $teacher = $this->teacher();
        $yahay = $this->student([
            'nom_complet' => 'Yahay',
            'login' => 'yahay@esipres.com',
            'matiere' => 'Mathématiques',
            'paiement' => '200.00',
        ]);
        $nada = $this->student([
            'nom_complet' => 'Nada',
            'login' => 'nada@esipres.com',
            'matiere' => 'Mathématiques',
            'paiement' => '200.00',
        ]);
        $lina = $this->student([
            'nom_complet' => 'Lina',
            'login' => 'lina@esipres.com',
            'matiere' => 'Mathématiques',
            'paiement' => '200.00',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.groups.store'), [
                'matiere' => 'Mathématiques',
                'niveau' => 'college',
                'teacher_id' => $teacher->id,
                'eleves' => [$yahay->id, $nada->id],
            ])
            ->assertRedirect(route('admin.page.groupes'))
            ->assertSessionHasNoErrors();

        $group = StudyGroup::query()->first();
        $this->assertSame(400.0, $group->revenue());
        $this->assertSame('400.00', $group->revenueDisplay());

        $this->actingAs($this->admin())
            ->get(route('admin.page.groupes', ['embed' => 1]))
            ->assertOk()
            ->assertSee('400.00')
            ->assertSee('id="groupRevenueValue"', false)
            ->assertSee('data-paiement="200.00"', false)
            ->assertDontSee('issus des demandes');

        $this->actingAs($this->admin())
            ->post(route('admin.groups.store'), [
                'group_id' => $group->id,
                'matiere' => 'Mathématiques',
                'niveau' => 'college',
                'teacher_id' => $teacher->id,
                'eleves' => [$yahay->id, $nada->id, $lina->id],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(600.0, $group->fresh()->load('students')->revenue());

        $this->actingAs($this->admin())
            ->get(route('admin.page.groupes', ['embed' => 1]))
            ->assertOk()
            ->assertSee('600.00');
    }

    public function test_one_student_subject_fee_is_not_multiplied_by_subject_count(): void
    {
        $teacher = $this->teacher();
        $student = $this->student([
            'matiere' => 'Mathématiques, Anglais',
            'paiement' => '200.00',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.groups.store'), [
                'matiere' => 'Mathématiques',
                'niveau' => 'college',
                'teacher_id' => $teacher->id,
                'eleves' => [$student->id],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(200.0, StudyGroup::query()->first()->revenue());

        $html = $this->actingAs($this->admin())
            ->get(route('admin.page.groupes', ['embed' => 1]))
            ->assertOk()
            ->assertSee('data-paiement="200.00"', false)
            ->assertDontSee('data-paiement="400.00"', false)
            ->assertSee('Paiement 200.00')
            ->assertSee('200.00')
            ->getContent();

        $this->assertStringNotContainsString('data-paiement="400', $html);
    }

    public function test_new_group_form_uses_subject_paiement_not_student_total(): void
    {
        $this->teacher();
        $this->student([
            'matiere' => 'Mathématiques, Anglais',
            'paiement' => '200.00',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.page.groupes', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Nouveau groupe')
            ->assertSee('groupRevenueValue')
            ->assertDontSee('issus des demandes')
            ->assertSee('data-paiement="200.00"', false)
            ->assertDontSee('data-paiement="400.00"', false)
            ->assertSee('Paiement 200.00')
            ->assertDontSee('Paiement 400.00');
    }

    public function test_group_store_rejects_a_student_from_another_subject(): void
    {
        $teacher = $this->teacher();
        $student = $this->student([
            'matiere' => 'Anglais',
            'niveau_scolaire' => 'college',
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.groups.store'), [
                'matiere' => 'Mathématiques',
                'niveau' => 'college',
                'teacher_id' => $teacher->id,
                'eleves' => [$student->id],
            ])
            ->assertSessionHasErrors('eleves');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
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
            'niveau_scolaire' => 'college',
            'matiere' => 'Mathématiques',
            'type_cours' => 'en_groupe',
            'etat' => Student::ETAT_ACTIF,
            'paiement' => '1200.00',
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
}
