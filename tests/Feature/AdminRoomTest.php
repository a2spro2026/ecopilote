<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\RoomController;
use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\StudySession;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_rooms_page_shows_cards_from_001_to_020(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.page.salles-actives', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Salles')
            ->assertSee('001')
            ->assertSee('020')
            ->assertSee('Libre')
            ->assertDontSee('Contenu à venir');
    }

    public function test_occupied_rooms_blink_according_to_session_status(): void
    {
        $group = $this->group();
        StudySession::create([
            'study_group_id' => $group->id,
            'date' => '2026-08-20',
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
            'numero_salle' => '001',
            'statut' => StudySession::STATUT_ACTIF,
        ]);
        StudySession::create([
            'study_group_id' => $group->id,
            'date' => '2026-08-20',
            'heure_debut' => '11:00',
            'heure_fin' => '12:00',
            'numero_salle' => 'S-02',
            'statut' => StudySession::STATUT_REPORTEE,
            'remarque' => 'Report',
        ]);
        StudySession::create([
            'study_group_id' => $group->id,
            'date' => '2026-08-20',
            'heure_debut' => '14:00',
            'heure_fin' => '16:00',
            'numero_salle' => '003',
            'statut' => StudySession::STATUT_ANNULEE,
            'remarque' => 'Annulée',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.page.salles-actives', ['embed' => 1]))
            ->assertOk()
            ->assertSee('salle-blink-actif', false)
            ->assertSee('salle-blink-reportee', false)
            ->assertSee('salle-blink-annulee', false)
            ->assertSee('Nadia El Amrani')
            ->assertSee('Fermer')
            ->assertSee('salleListenLink', false)
            ->assertSee('Écouter');
    }

    public function test_admin_can_listen_to_active_room_session(): void
    {
        $group = $this->group();
        $session = StudySession::create([
            'study_group_id' => $group->id,
            'date' => '2026-08-20',
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
            'numero_salle' => '001',
            'statut' => StudySession::STATUT_ACTIF,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.rooms.listen', $session))
            ->assertOk()
            ->assertSee('Écoute de la salle')
            ->assertSee('Salle 001')
            ->assertSee('Nadia El Amrani')
            ->assertSee('Yasmine Alaoui');
    }

    public function test_admin_can_watch_active_room_session(): void
    {
        $group = $this->group();
        $session = StudySession::create([
            'study_group_id' => $group->id,
            'date' => '2026-08-20',
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
            'numero_salle' => '001',
            'statut' => StudySession::STATUT_ACTIF,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.rooms.watch', $session))
            ->assertOk()
            ->assertSee('Observation visuelle')
            ->assertSee('Tableau')
            ->assertSee('Cours en cours');
    }

    public function test_admin_cannot_observe_cancelled_session(): void
    {
        $group = $this->group();
        $session = StudySession::create([
            'study_group_id' => $group->id,
            'date' => '2026-08-20',
            'heure_debut' => '09:00',
            'heure_fin' => '10:00',
            'numero_salle' => '001',
            'statut' => StudySession::STATUT_ANNULEE,
            'remarque' => 'Annulée',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.rooms.listen', $session))
            ->assertForbidden();

        $this->actingAs($this->admin())
            ->get(route('admin.rooms.watch', $session))
            ->assertForbidden();
    }

    public function test_room_code_is_normalized_from_session_number(): void
    {
        $this->assertSame('001', RoomController::normalizeRoomCode('001'));
        $this->assertSame('001', RoomController::normalizeRoomCode('S-01'));
        $this->assertSame('020', RoomController::normalizeRoomCode('Salle 20'));
        $this->assertNull(RoomController::normalizeRoomCode('Salle 21'));
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
