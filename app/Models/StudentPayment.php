<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPayment extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'matiere',
        'montant',
        'mode_paiement',
        'montant_paye',
        'solde',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'montant' => 'decimal:2',
            'montant_paye' => 'decimal:2',
            'solde' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function modeLabel(): string
    {
        return match ($this->mode_paiement) {
            'virement' => 'Virement',
            'cheque' => 'Chèque',
            'especes' => 'Espèces',
            'versement' => 'Versement',
            default => $this->mode_paiement ?: '—',
        };
    }

    public static function money(float|string|null $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return number_format((float) $value, 2, '.', '');
    }
}
