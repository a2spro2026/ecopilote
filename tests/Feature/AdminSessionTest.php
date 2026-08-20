<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\StudySession;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_page_shows_the_table_and_add_form(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.page.seances', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Séance')
            ->assertSee('Date')
            ->assertSee('N°/Sé')
            ->assertSee('Groupe')
            ->assertSee('Matière')
            ->assertSee('Niveau')
            ->assertSee('Prof')
            ->assertSee('Effectif')
            ->assertSee('Hr Début')
            ->assertSee('Hr Fin')
            ->assertSee('N° Salle')
            ->assertSee('Statut')
            ->assertSee('Remarque')
            ->assertSee('Ajouter')
            ->assertSee('Nouvelle séance')
            ->assertSee('SE-0001')
            ->assertDontSee('Contenu à venir');
    }

    public function test_superadmin_can_create_a_session_from_a_group(): void
    {
        $group = $this->group();

        $this->actingAs($this->admin())
            ->post(route('admin.sessions.store'), [
                'study_group_id' => $group->id,
                'date' => '2026-08-25',
                'heure_debut' => '14:00',
                'heure_fin' => '16:00',
                'numero_salle' => 'S-01',
            ])
            ->assertRedirect(route('admin.page.seances'))
            ->assertSessionHasNoErrors();

        $session = StudySession::query()->first();
        $this->assertNotNull($session);
        $this->assertSame('SE-0001', $session->displayId());
        $this->assertSame('actif', $session->statut);
        $this->assertNull($session->remarque);

        $this->actingAs($this->admin())
            ->get(route('admin.page.seances', ['embed' => 1]))
            ->assertOk()
            ->assertSee('SE-0001')
            ->assertSee('GR-0001')
            ->assertSee('Math')
            ->assertSee('Nadia El Amrani')
            ->assertSee('14:00')
            ->assertSee('16:00')
            ->assertSee('S-01')
            ->assertSee('Actif');
    }

    public function test_session_end_time_must_be_after_start_time(): void
    {
        $group = $this->group();

        $this->actingAs($this->admin())
            ->post(route('admin.sessions.store'), [
                'study_group_id' => $group->id,
                'date' => '2026-08-25',
                'heure_debut' => '16:00',
                'heure_fin' => '14:00',
                'numero_salle' => 'S-01',
            ])
            ->assertSessionHasErrors('heure_fin');
    }

    public function test_session_status_update_requires_remark_when_postponed_or_cancelled(): void
    {
        $session = $this->makeSession();

        $this->actingAs($this->admin())
            ->patch(route('admin.sessions.update', $session), [
                '_statut_row' => '1',
                'statut' => 'reportee',
                'remarque' => '',
            ])
            ->assertSessionHasErrors('remarque');

        $this->actingAs($this->admin())
            ->patch(route('admin.sessions.update', $session), [
                '_statut_row' => '1',
                'statut' => 'reportee',
                'remarque' => 'Prof indisponible',
            ])
            ->assertRedirect(route('admin.page.seances'))
            ->assertSessionHasNoErrors();

        $session->refresh();
        $this->assertSame('reportee', $session->statut);
        $this->assertSame('Prof indisponible', $session->remarque);

        $this->actingAs($this->admin())
            ->patch(route('admin.sessions.update', $session), [
                '_statut_row' => '1',
                'statut' => 'actif',
                'remarque' => 'ignored',
            ])
            ->assertSessionHasNoErrors();

        $session->refresh();
        $this->assertSame('actif', $session->statut);
        $this->assertNull($session->remarque);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }

    private function group(): StudyGroup
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

        $student = Student::create([
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
        ]);

        $group = StudyGroup::create([
            'matiere' => 'Mathématiques',
            'niveau' => 'college',
            'teacher_id' => $teacher->id,
        ]);
        $group->students()->sync([$student->id]);

        return $group->fresh(['teacher', 'students']);
    }

    private function makeSession(): StudySession
    {
        return StudySession::create([
            'study_group_id' => $this->group()->id,
            'date' => '2026-08-25',
            'heure_debut' => '14:00',
            'heure_fin' => '16:00',
            'numero_salle' => 'S-01',
            'statut' => StudySession::STATUT_ACTIF,
        ]);
    }
}
