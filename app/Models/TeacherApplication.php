<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherApplication extends Model
{
    public const ETAT_EN_ATTENTE = 'en_attente';

    public const ETAT_VALIDEE = 'validee';

    public const ETAT_SUSPENDUE = 'suspendue';

    public const ETAT_REFUSEE = 'refusee';

    protected $fillable = [
        'nom_complet',
        'contact',
        'ville',
        'matiere',
        'niveau',
        'statut',
        'disponibilite',
        'etat',
        'teacher_id',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function niveauLabel(): string
    {
        return match ($this->niveau) {
            'primaire' => 'Primaire',
            'college' => 'Collège',
            'lycee' => 'Lycée',
            'universitaire' => 'Universitaire',
            default => (string) $this->niveau,
        };
    }

    public function statutLabel(): string
    {
        return match ($this->statut) {
            'public' => 'Public',
            'prive' => 'Privé',
            default => (string) $this->statut,
        };
    }

    public function disponibiliteLabel(): string
    {
        return match ($this->disponibilite) {
            'immediat' => 'Immédiat',
            'a_negocier' => 'À négocier',
            default => (string) $this->disponibilite,
        };
    }

    public function etatLabel(): string
    {
        return match ($this->etat) {
            self::ETAT_EN_ATTENTE => 'En attente',
            self::ETAT_VALIDEE => 'Validée',
            self::ETAT_SUSPENDUE => 'Suspendue',
            self::ETAT_REFUSEE => 'Refusée',
            default => (string) $this->etat,
        };
    }
}
