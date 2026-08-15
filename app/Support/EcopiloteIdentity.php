<?php

namespace App\Support;

use Illuminate\Support\Str;

class EcopiloteIdentity
{
    public const DOMAIN = 'ecopilote.ma';

    public static function emailSuffix(): string
    {
        return '@'.self::DOMAIN;
    }

    /**
     * Complète un identifiant avec @ecopilote.ma s’il n’a pas déjà de domaine.
     */
    public static function email(string $value): string
    {
        $value = trim($value);

        if ($value === '' || str_contains($value, '@')) {
            return $value;
        }

        return $value.self::emailSuffix();
    }

    /**
     * Partie locale à afficher dans le champ (sans le suffixe ECOPILOTE).
     */
    public static function localPart(?string $email): string
    {
        $email = trim((string) $email);

        if ($email === '') {
            return '';
        }

        $suffix = self::emailSuffix();
        if (str_ends_with(strtolower($email), $suffix)) {
            return substr($email, 0, -strlen($suffix));
        }

        return $email;
    }

    /**
     * Login professeur dérivé du nom : « Nadia El Amrani » → nadia.el.amrani@ecopilote.ma
     */
    public static function loginFromName(string $name): string
    {
        $local = Str::of($name)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/u', '.')
            ->trim('.')
            ->toString();

        return self::email($local !== '' ? $local : 'prof');
    }
}
