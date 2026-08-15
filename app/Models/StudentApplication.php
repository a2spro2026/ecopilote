<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentApplication extends Model
{
    public const ETAT_EN_ATTENTE = 'en_attente';

    public const ETAT_VALIDEE = 'validee';

    public const ETAT_SUSPENDUE = 'suspendue';

    protected $fillable = [
        'nom_complet',
        'contact',
        'contact_tuteur',
        'ville',
        'niveau_scolaire',
        'matiere',
        'type_cours',
        'etat',
        'student_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function typeCoursLabel(): string
    {
        return match ($this->type_cours) {
            'individuel' => 'Individuel',
            'en_groupe' => 'En Groupe',
            default => (string) $this->type_cours,
        };
    }

    public function etatLabel(): string
    {
        return match ($this->etat) {
            self::ETAT_EN_ATTENTE => 'En attente',
            self::ETAT_VALIDEE => 'Validée',
            self::ETAT_SUSPENDUE => 'Suspendue',
            default => (string) $this->etat,
        };
    }
}
