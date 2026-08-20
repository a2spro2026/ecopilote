<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Support\SubjectAbbreviation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentPaymentController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $payments = StudentPayment::query()
            ->with('student')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $students = Student::query()
            ->where('etat', '!=', Student::ETAT_SUSPENDU)
            ->orderBy('nom_complet')
            ->get();

        return view('admin.students.payments', [
            'payments' => $payments,
            'students' => $students,
            'months' => $this->months(),
            'currentMonth' => now()->format('Y-m'),
            'matieres' => $this->subjects(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'payment_id' => ['nullable', 'integer', 'exists:student_payments,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'date' => ['required', 'date'],
            'matiere' => ['required', 'string', Rule::in($this->subjects())],
            'montant' => ['required', 'numeric', 'min:0'],
            'mode_paiement' => ['required', Rule::in(['virement', 'cheque', 'especes', 'versement'])],
            'montant_paye' => ['required', 'numeric', 'min:0'],
        ]);

        $payload = [
            'student_id' => $data['student_id'],
            'date' => $data['date'],
            'matiere' => $data['matiere'],
            'montant' => number_format((float) $data['montant'], 2, '.', ''),
            'mode_paiement' => $data['mode_paiement'],
            'montant_paye' => number_format((float) $data['montant_paye'], 2, '.', ''),
            'solde' => number_format((float) $data['montant'] - (float) $data['montant_paye'], 2, '.', ''),
        ];

        if (! empty($data['payment_id'])) {
            $payment = StudentPayment::findOrFail($data['payment_id']);
            $payment->update($payload);
            $message = 'Fiche paiement mise à jour.';
        } else {
            StudentPayment::create($payload);
            $message = 'Fiche paiement ajoutée.';
        }

        return redirect()
            ->route('admin.page.fiche-paiement-eleve')
            ->with('status', $message);
    }

    public function print(Request $request, StudentPayment $payment)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $payment->load('student');

        return view('admin.students.payment-print', [
            'payment' => $payment,
            'matiereLabel' => SubjectAbbreviation::display($payment->matiere),
        ]);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function months(): array
    {
        $labels = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        $items = [['value' => '', 'label' => 'Tous les mois']];
        $cursor = now()->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $date = $cursor->copy()->subMonths($i);
            $items[] = [
                'value' => $date->format('Y-m'),
                'label' => $labels[(int) $date->format('n')].' '.$date->format('Y'),
            ];
        }

        return $items;
    }

    /**
     * @return list<string>
     */
    private function subjects(): array
    {
        return [
            'Mathématiques',
            'Physique-Chimie',
            'Français',
            'Anglais',
            'SVT',
            'Histoire-Géographie',
            'Informatique',
            'Arabe',
        ];
    }
}
