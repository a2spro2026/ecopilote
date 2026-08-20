<?php

namespace App\Support;

class SubjectAbbreviation
{
    /**
     * @var array<string, string>
     */
    private const MAP = [
        'mathematiques' => 'Math',
        'physique-chimie' => 'PC',
        'physique chimie' => 'PC',
        'francais' => 'Fr',
        'anglais' => 'Ang',
        'svt' => 'SVT',
        'histoire-geographie' => 'HG',
        'histoire geographie' => 'HG',
        'informatique' => 'Info',
        'arabe' => 'Ar',
    ];

    public static function display(?string $raw): string
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return '—';
        }

        $parts = preg_split('/[,;\/|]+/', $raw) ?: [];
        $labels = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            $labels[] = self::MAP[self::fold($part)] ?? $part;
        }

        return $labels === [] ? '—' : implode(', ', $labels);
    }

    private static function fold(string $value): string
    {
        $value = trim(mb_strtolower($value, 'UTF-8'));

        return strtr($value, [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'œ' => 'oe',
        ]);
    }
}
