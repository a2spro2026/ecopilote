<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStudentPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_page_shows_the_table_and_search_bar(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.page.fiche-paiement-eleve', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Fiche paiement')
            ->assertSee('Mois')
            ->assertSee('Nom')
            ->assertSee('Montant')
            ->assertSee('Date')
            ->assertSee('Nom Complet')
            ->assertSee('Matière')
            ->assertSee('Mode')
            ->assertSee('Solde')
            ->assertSee('Ajouter')
            ->assertSee('Valider')
            ->assertDontSee('Contenu à venir');
    }

    public function test_superadmin_can_create_and_print_a_payment_fiche(): void
    {
        $student = $this->student();

        $this->actingAs($this->admin())
            ->post(route('admin.students.payments.store'), [
                'student_id' => $student->id,
                'date' => '2026-08-20',
                'matiere' => 'Mathématiques',
                'montant' => '1200',
                'mode_paiement' => 'virement',
                'montant_paye' => '800',
            ])
            ->assertRedirect(route('admin.page.fiche-paiement-eleve'))
            ->assertSessionHasNoErrors();

        $payment = StudentPayment::query()->first();
        $this->assertNotNull($payment);
        $this->assertSame('Mathématiques', $payment->matiere);
        $this->assertSame(1200.0, (float) $payment->montant);
        $this->assertSame(800.0, (float) $payment->montant_paye);
        $this->assertSame(400.0, (float) $payment->solde);

        $this->actingAs($this->admin())
            ->get(route('admin.page.fiche-paiement-eleve', ['embed' => 1]))
            ->assertOk()
            ->assertSee('Yasmine Alaoui')
            ->assertSee('Math')
            ->assertSee('800.00')
            ->assertSee('400.00');

        $this->actingAs($this->admin())
            ->get(route('admin.students.payments.print', $payment))
            ->assertOk()
            ->assertSee('Fiche paiement')
            ->assertSee('Yasmine Alaoui')
            ->assertSee('Math');
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
            'niveau_scolaire' => 'college',
            'matiere' => 'Mathématiques, Anglais',
            'type_cours' => 'en_groupe',
            'etat' => Student::ETAT_ACTIF,
            'paiement' => '1200.00',
            'mode_paiement' => 'virement',
            'periode_paiement' => 'mois',
        ]);
    }
}
