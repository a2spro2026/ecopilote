<?php

namespace App\Support;

class UppercaseText
{
    /**
     * Champs techniques ou énumérés : la casse doit rester intacte
     * (mots de passe, identifiants, clés de validation).
     *
     * @var list<string>
     */
    public const SKIP_KEYS = [
        '_token',
        '_method',
        '_form',
        'password',
        'password_confirmation',
        'current_password',
        'access_password',
        'login',
        'email',
        'photo',
        'video',
        'file',
        'files',
        'remember',
        'embed',
        'mode_paiement',
        'periode_paiement',
        'type_paiement',
        'type_cours',
        'etat',
        'statut',
        'disponibilite',
        'role',
        'niveau',
        'type',
        'matiere',
        'matieres',
        'jours',
        'heure_debut',
        'heure_fin',
        'date_debut',
        'date_fin',
        'sans_date_fin',
        'paiement',
        'paiement_valeur',
        'echeance',
    ];

    public static function convert(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function convertPayload(array $data, ?string $parentKey = null): array
    {
        $converted = [];

        foreach ($data as $key => $value) {
            $name = strtolower((string) $key);
            $parent = strtolower((string) $parentKey);

            if (self::shouldSkip($name, $parent)) {
                $converted[$key] = $value;

                continue;
            }

            if (is_array($value)) {
                $converted[$key] = self::convertPayload($value, $name);

                continue;
            }

            if (! is_string($value) || $value === '') {
                $converted[$key] = $value;

                continue;
            }

            $converted[$key] = self::convert($value);
        }

        return $converted;
    }

    private static function shouldSkip(string $name, string $parent): bool
    {
        if ($name === 'id' || str_ends_with($name, '_id')) {
            return true;
        }

        if (str_starts_with($name, '_')) {
            return true;
        }

        return in_array($name, self::SKIP_KEYS, true)
            || in_array($parent, self::SKIP_KEYS, true);
    }
}
