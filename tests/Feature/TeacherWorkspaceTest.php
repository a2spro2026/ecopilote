<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\StudySession;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_teacher_workspace(): void
    {
        $this->get('/espace-prof')->assertRedirect(route('portail.profs'));
    }

    public function test_teacher_portal_is_available(): void
    {
        $this->get('/portail-profs')
            ->assertOk()
            ->assertSee('@esipres.com')
            ->assertSee('votre.identifiant')
            ->assertSee('Matières enseignées')
            ->assertSee('Physique-Chimie');
    }

    public function test_teacher_can_apply_with_multiple_subjects(): void
    {
        $this->post('/portail-profs/inscription', [
            '_form' => 'prof_register',
            'nom_complet' => 'Nadia El Amrani',
            'contact' => '0600000000',
            'ville' => 'Casablanca',
            'matieres' => ['Mathématiques', 'Physique-Chimie', 'SVT'],
            'niveau' => 'college',
            'statut' => 'prive',
            'disponibilite' => 'immediat',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('teacher_applications', [
            'nom_complet' => 'NADIA EL AMRANI',
            'matiere' => 'Mathématiques, Physique-Chimie, SVT',
        ]);
    }

    public function test_teacher_application_requires_at_least_one_subject(): void
    {
        $this->from('/portail-profs')->post('/portail-profs/inscription', [
            '_form' => 'prof_register',
            'nom_complet' => 'Nadia El Amrani',
            'contact' => '0600000000',
            'ville' => 'Casablanca',
            'matieres' => [],
            'niveau' => 'college',
            'statut' => 'prive',
            'disponibilite' => 'immediat',
        ])->assertRedirect('/portail-profs')->assertSessionHasErrors('matieres');
    }

    public function test_student_portal_is_unchanged(): void
    {
        $this->get('/portail-etudiant')->assertOk();
    }

    public function test_admin_login_is_unchanged(): void
    {
        $this->get('/administration/login')
            ->assertOk()
            ->assertSee('@esipres.com')
            ->assertSee('votre.identifiant');
        $this->get('/administration')->assertRedirect(route('admin.login'));
    }

    public function test_student_portal_shows_ecopilote_email_suffix(): void
    {
        $this->get('/portail-etudiant')
            ->assertOk()
            ->assertSee('@esipres.com')
            ->assertSee('votre.identifiant')
            ->assertSee('Importer');
    }

    public function test_active_teacher_is_redirected_to_workspace_after_login(): void
    {
        $teacher = $this->makeTeacher();
        $session = $this->makeAssignedSession($teacher);

        $this->post('/portail-profs', [
            'login' => 'nadia.el.amrani',
            'password' => 'secret12',
        ])->assertRedirect(route('teacher.bureau'));

        $this->get('/espace-prof')->assertOk()->assertSee('Mon Bureau');
        $this->get('/espace-prof/classes')
            ->assertOk()
            ->assertSee('Mes Classes')
            ->assertSee('Retour à la salle');
        $this->get('/espace-prof/seances')
            ->assertOk()
            ->assertSee('Mes Séances')
            ->assertSee('GR-0001')
            ->assertSee('Rejoindre');
        $this->get('/espace-prof/eleves')->assertOk()->assertSee('Mes Élèves');
        $this->get('/espace-prof/bibliotheque')
            ->assertOk()
            ->assertDontSee('Supprimer')
            ->assertDontSee('Archiver');
        $this->get('/espace-prof/exercices')->assertOk();
        $this->get('/espace-prof/archives')
            ->assertOk()
            ->assertSee('Archives en lecture seule')
            ->assertDontSee('Restaurer')
            ->assertDontSee('Supprimer définitivement');
        $this->get('/espace-prof/suivi')->assertOk();
        $this->get('/espace-prof/notifications')->assertOk();
        $this->get('/espace-prof/profil')->assertOk()->assertSee('Professeur validé');
        $this->get('/espace-prof/salle')
            ->assertRedirect(route('teacher.salle.show', $session));
        $this->get('/espace-prof/salle/'.$session->id)
            ->assertOk()
            ->assertSee('EN DIRECT')
            ->assertSee('Salle 001')
            ->assertSee('Élève '.$teacher->id)
            ->assertSee('Clavier')
            ->assertSee('Bibliothèque de formes')
            ->assertSee('Lignes de cahier')
            ->assertSee('Carreaux')
            ->assertSee('Signes mathématiques')
            ->assertSee('Racine carrée')
            ->assertSee('Triangle rectangle')
            ->assertSee('Étoile')
            ->assertSee('Activer le micro')
            ->assertSee('Activer la caméra')
            ->assertSee('Partager l’écran')
            ->assertSee('Enregistrement automatique')
            ->assertSee('Chronomètre')
            ->assertSee('Vue élève')
            ->assertSee('Lever la main')
            ->assertSee('Envoyer au professeur');
        $this->get('/espace-prof/seance-terminee')->assertOk()->assertSee('CONSULTER');
    }

    public function test_teacher_cannot_join_cancelled_or_unassigned_room(): void
    {
        $teacher = $this->makeTeacher();
        $group = $this->makeGroup($teacher);

        $cancelled = StudySession::create([
            'study_group_id' => $group->id,
            'date' => now()->toDateString(),
            'heure_debut' => '10:00',
            'heure_fin' => '12:00',
            'numero_salle' => '001',
            'statut' => StudySession::STATUT_ANNULEE,
        ]);

        $unassigned = StudySession::create([
            'study_group_id' => $group->id,
            'date' => now()->addDay()->toDateString(),
            'heure_debut' => '10:00',
            'heure_fin' => '12:00',
            'numero_salle' => '',
            'statut' => StudySession::STATUT_ACTIF,
        ]);

        $this->actingAsTeacher($teacher)
            ->get(route('teacher.salle.show', $cancelled))
            ->assertForbidden();

        $this->actingAsTeacher($teacher)
            ->get(route('teacher.salle.show', $unassigned))
            ->assertForbidden();

        $this->actingAsTeacher($teacher)
            ->get(route('teacher.salle'))
            ->assertRedirect(route('teacher.bureau'));
    }

    public function test_teacher_only_sees_their_own_assigned_sessions(): void
    {
        $teacher = $this->makeTeacher();
        $otherTeacher = $this->makeTeacher([
            'nom_complet' => 'Karim Benali',
            'login' => 'karim.benali@esipres.com',
            'matiere' => 'Physique-Chimie',
        ]);

        $ownSession = $this->makeAssignedSession($teacher);
        $foreignGroup = $this->makeGroup($otherTeacher);
        $foreignSession = StudySession::create([
            'study_group_id' => $foreignGroup->id,
            'date' => now()->toDateString(),
            'heure_debut' => '08:00',
            'heure_fin' => '09:00',
            'numero_salle' => '002',
            'statut' => StudySession::STATUT_ACTIF,
        ]);

        $this->actingAsTeacher($teacher)
            ->get(route('teacher.seances'))
            ->assertOk()
            ->assertSee('GR-0001')
            ->assertDontSee('GR-0002');

        $this->actingAsTeacher($teacher)
            ->get(route('teacher.salle.show', $foreignSession))
            ->assertNotFound();
    }

    public function test_suspended_teacher_cannot_access_workspace(): void
    {
        $teacher = $this->makeTeacher(['etat' => Teacher::ETAT_SUSPENDU]);

        $this->post('/portail-profs', [
            'login' => $teacher->login,
            'password' => 'secret12',
        ])->assertRedirect();

        $this->withSession(['teacher_id' => $teacher->id])
            ->get('/espace-prof')
            ->assertRedirect(route('portail.profs'));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeTeacher(array $overrides = []): Teacher
    {
        return Teacher::query()->create(array_merge([
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
        ], $overrides));
    }

    private function actingAsTeacher(Teacher $teacher): self
    {
        return $this->withSession(['teacher_id' => $teacher->id]);
    }

    private function makeGroup(Teacher $teacher): StudyGroup
    {
        $student = Student::create([
            'nom_complet' => 'Élève '.$teacher->id,
            'login' => 'eleve.'.$teacher->id.'@esipres.com',
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

    private function makeAssignedSession(Teacher $teacher): StudySession
    {
        $group = $this->makeGroup($teacher);

        return StudySession::create([
            'study_group_id' => $group->id,
            'date' => now()->toDateString(),
            'heure_debut' => '00:00',
            'heure_fin' => '23:59',
            'numero_salle' => '001',
            'statut' => StudySession::STATUT_ACTIF,
        ]);
    }
}
