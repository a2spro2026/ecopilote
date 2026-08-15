<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    public const ETAT_ACTIF = 'actif';

    public const ETAT_EN_ATTENTE = 'en_attente';

    public const ETAT_SUSPENDU = 'suspendu';

    protected $fillable = [
        'code',
        'nom_complet',
        'login',
        'access_password',
        'contact',
        'tuteur_nom',
        'contact_tuteur',
        'ville',
        'niveau_scolaire',
        'matiere',
        'type_cours',
        'etat',
        'paiement',
        'mode_paiement',
        'periode_paiement',
        'photo_path',
        'echeance',
    ];

    protected function casts(): array
    {
        return [
            'echeance' => 'date',
            'paiement' => 'decimal:2',
            'access_password' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Student $student) {
            if (blank($student->code)) {
                $student->forceFill([
                    'code' => 'EL'.str_pad((string) $student->id, 4, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    public function application(): HasOne
    {
        return $this->hasOne(StudentApplication::class);
    }

    public function displayId(): string
    {
        return $this->code ?: ('EL'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
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
            self::ETAT_ACTIF => 'Actif',
            self::ETAT_EN_ATTENTE => 'En Attente',
            self::ETAT_SUSPENDU => 'Suspendu',
            default => (string) $this->etat,
        };
    }

    public function paiementDisplay(): string
    {
        if ($this->paiement === null || $this->paiement === '') {
            return '—';
        }

        return number_format((float) $this->paiement, 2, '.', '');
    }

    public function echeanceDisplay(): string
    {
        return $this->echeance ? $this->echeance->format('d/m/Y') : '—';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path || ! Storage::disk('public')->exists($this->photo_path)) {
            return null;
        }

        // asset() suit l'hôte de la requête courante, contrairement à Storage::url() qui fige APP_URL.
        return asset('storage/'.ltrim(str_replace('\\', '/', $this->photo_path), '/'));
    }
}
