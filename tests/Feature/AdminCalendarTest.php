<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\StudySession;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_page_shows_hour_day_week_views(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.page.calendrier', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Calendrier')
            ->assertSee('Heure')
            ->assertSee('Jour')
            ->assertSee('Semaine')
            ->assertSee('Actif')
            ->assertSee('Reportée')
            ->assertSee('Annulée')
            ->assertDontSee('Contenu à venir');
    }

    public function test_calendar_shows_colored_session_buttons_and_info_panel(): void
    {
        $group = $this->group();
        StudySession::create([
            'study_group_id' => $group->id,
            'date' => '2026-08-20',
            'heure_debut' => '14:00',
            'heure_fin' => '16:00',
            'numero_salle' => 'S-01',
            'statut' => StudySession::STATUT_ACTIF,
        ]);
        StudySession::create([
            'study_group_id' => $group->id,
            'date' => '2026-08-20',
            'heure_debut' => '16:00',
            'heure_fin' => '18:00',
            'numero_salle' => 'S-02',
            'statut' => StudySession::STATUT_REPORTEE,
            'remarque' => 'Prof indisponible',
        ]);
        StudySession::create([
            'study_group_id' => $group->id,
            'date' => '2026-08-20',
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
            'numero_salle' => 'S-03',
            'statut' => StudySession::STATUT_ANNULEE,
            'remarque' => 'Jour férié',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.page.calendrier', ['embed' => 1, 'vue' => 'jour', 'date' => '2026-08-20']))
            ->assertOk()
            ->assertSee('bg-emerald-500', false)
            ->assertSee('bg-amber-400', false)
            ->assertSee('bg-rose-500', false)
            ->assertSee('data-open-session', false)
            ->assertSee('Fiche séance')
            ->assertSee('Fermer')
            ->assertSee('Nadia El Amrani')
            ->assertSee('Yasmine Alaoui')
            ->assertSee('Math');
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
}
