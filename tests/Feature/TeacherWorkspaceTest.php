<?php

namespace Tests\Feature;

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
            ->assertSee('@ecopilote.ma')
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
            'nom_complet' => 'Nadia El Amrani',
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
            ->assertSee('@ecopilote.ma')
            ->assertSee('zerragui', false);
        $this->get('/administration')->assertRedirect(route('admin.login'));
    }

    public function test_student_portal_shows_ecopilote_email_suffix(): void
    {
        $this->get('/portail-etudiant')
            ->assertOk()
            ->assertSee('@ecopilote.ma')
            ->assertSee('votre.identifiant');
    }

    public function test_active_teacher_is_redirected_to_workspace_after_login(): void
    {
        $teacher = $this->makeTeacher();

        $this->post('/portail-profs', [
            'login' => 'nadia.el.amrani',
            'password' => 'secret12',
        ])->assertRedirect(route('teacher.bureau'));

        $this->get('/espace-prof')->assertOk()->assertSee('Mon Bureau');
        $this->get('/espace-prof/classes')
            ->assertOk()
            ->assertSee('Mes Classes')
            ->assertSee('Retour à la salle');
        $this->get('/espace-prof/classes/1')->assertOk();
        $this->get('/espace-prof/seances')->assertOk()->assertSee('Mes Séances');
        $this->get('/espace-prof/eleves')->assertOk()->assertSee('Mes Élèves');
        $this->get('/espace-prof/eleves/1')->assertOk();
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
            ->assertOk()
            ->assertSee('EN DIRECT')
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
            'login' => 'nadia.el.amrani@ecopilote.ma',
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
}
