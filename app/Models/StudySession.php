<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudySession extends Model
{
    public const STATUT_ACTIF = 'actif';

    public const STATUT_REPORTEE = 'reportee';

    public const STATUT_ANNULEE = 'annulee';

    protected $fillable = [
        'code',
        'study_group_id',
        'date',
        'heure_debut',
        'heure_fin',
        'numero_salle',
        'statut',
        'remarque',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (StudySession $session) {
            if (blank($session->code)) {
                $session->forceFill([
                    'code' => 'SE-'.str_pad((string) $session->id, 4, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function displayId(): string
    {
        return $this->code ?: ('SE-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
    }

    public function statutLabel(): string
    {
        return match ($this->statut) {
            self::STATUT_ACTIF => 'Actif',
            self::STATUT_REPORTEE => 'Reportée',
            self::STATUT_ANNULEE => 'Annulée',
            default => (string) $this->statut,
        };
    }

    public function heureDebutDisplay(): string
    {
        return substr((string) $this->heure_debut, 0, 5);
    }

    public function heureFinDisplay(): string
    {
        return substr((string) $this->heure_fin, 0, 5);
    }

    public function dateDisplay(): string
    {
        return $this->date?->format('d/m/Y') ?: '—';
    }
}
