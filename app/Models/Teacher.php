<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends Model
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
        'ville',
        'statut',
        'matiere',
        'niveau',
        'disponibilite',
        'etat',
        'paiement',
        'paiement_valeur',
        'type_paiement',
    ];

    protected function casts(): array
    {
        return [
            'access_password' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Teacher $teacher) {
            if (blank($teacher->code)) {
                $teacher->forceFill([
                    'code' => 'PF'.str_pad((string) $teacher->id, 4, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    public function application(): HasOne
    {
        return $this->hasOne(TeacherApplication::class);
    }

    public function displayId(): string
    {
        return $this->code ?: ('PF'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
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
            self::ETAT_ACTIF => 'Actif',
            self::ETAT_EN_ATTENTE => 'En Attente',
            self::ETAT_SUSPENDU => 'Suspendu',
            default => (string) $this->etat,
        };
    }

    public function paiementLabel(): string
    {
        return match ($this->paiement) {
            'salaire' => 'Salaire',
            'commission' => 'Commission',
            'pourcentage' => 'Pourcentage',
            default => '—',
        };
    }

    public function montantDisplay(): string
    {
        if ($this->paiement_valeur === null || $this->paiement_valeur === '') {
            return '—';
        }

        $amount = number_format((float) $this->paiement_valeur, 2, '.', '');

        return $this->paiement === 'pourcentage' ? $amount.' %' : $amount;
    }

    public function typePaiementLabel(): string
    {
        return match ($this->type_paiement) {
            'vir' => 'Vir',
            'chq' => 'Chq',
            'vers' => 'Vers',
            'esp' => 'Esp',
            default => '—',
        };
    }
}
