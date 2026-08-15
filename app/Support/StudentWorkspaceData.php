<?php

namespace App\Support;

use App\Models\Student;

class StudentWorkspaceData
{
    public static function for(Student $student): array
    {
        return [
            'student' => $student,
            'classes' => [
                ['id' => 1, 'name' => 'Mathématiques · Collège', 'teacher' => 'Mme Nadia El Amrani', 'level' => $student->niveau_scolaire, 'progress' => 72, 'next' => 'Lundi · 17:00'],
                ['id' => 2, 'name' => 'Physique-Chimie', 'teacher' => 'M. Karim Bennis', 'level' => $student->niveau_scolaire, 'progress' => 61, 'next' => 'Mercredi · 18:00'],
                ['id' => 3, 'name' => 'Français', 'teacher' => 'Mme Salma Idrissi', 'level' => $student->niveau_scolaire, 'progress' => 84, 'next' => 'Vendredi · 16:30'],
            ],
            'sessions' => [
                ['subject' => 'Mathématiques', 'title' => 'Équations du premier degré', 'teacher' => 'Mme Nadia El Amrani', 'date' => '17 août 2026', 'time' => '17:00', 'status' => 'À venir', 'live' => true],
                ['subject' => 'Physique-Chimie', 'title' => 'Mouvement et vitesse', 'teacher' => 'M. Karim Bennis', 'date' => '19 août 2026', 'time' => '18:00', 'status' => 'À venir', 'live' => false],
                ['subject' => 'Français', 'title' => 'Le texte argumentatif', 'teacher' => 'Mme Salma Idrissi', 'date' => '14 août 2026', 'time' => '16:30', 'status' => 'Terminée', 'live' => false],
            ],
            'assignments' => [
                ['id' => 1, 'subject' => 'Mathématiques', 'title' => 'Série 4 · Équations', 'due' => '20 août 2026', 'status' => 'À rendre', 'score' => null],
                ['id' => 2, 'subject' => 'Physique-Chimie', 'title' => 'Compte rendu · Vitesse', 'due' => '23 août 2026', 'status' => 'À rendre', 'score' => null],
                ['id' => 3, 'subject' => 'Français', 'title' => 'Rédaction argumentée', 'due' => '12 août 2026', 'status' => 'Corrigé', 'score' => '16/20'],
            ],
            'documents' => [
                ['type' => 'PDF', 'title' => 'Fiche méthode · Résoudre une équation', 'subject' => 'Mathématiques', 'size' => '1,2 Mo'],
                ['type' => 'VIDÉO', 'title' => 'Comprendre la vitesse moyenne', 'subject' => 'Physique-Chimie', 'size' => '08:42'],
                ['type' => 'PDF', 'title' => 'Connecteurs logiques', 'subject' => 'Français', 'size' => '640 Ko'],
            ],
            'progress' => [
                ['subject' => 'Mathématiques', 'value' => 72, 'average' => '15,5/20', 'trend' => '+8 %'],
                ['subject' => 'Physique-Chimie', 'value' => 61, 'average' => '13/20', 'trend' => '+3 %'],
                ['subject' => 'Français', 'value' => 84, 'average' => '16/20', 'trend' => '+11 %'],
            ],
            'archives' => [
                ['title' => 'Séance · Fractions et priorités', 'date' => '8 août 2026', 'type' => 'Enregistrement'],
                ['title' => 'Correction · Série 3', 'date' => '5 août 2026', 'type' => 'Document'],
                ['title' => 'Séance · Les forces', 'date' => '1 août 2026', 'type' => 'Enregistrement'],
            ],
            'notifications' => [
                ['title' => 'Votre devoir de français a été corrigé', 'detail' => 'Note obtenue : 16/20', 'time' => 'Il y a 2 h', 'unread' => true],
                ['title' => 'Nouvelle ressource en mathématiques', 'detail' => 'Fiche méthode · Résoudre une équation', 'time' => 'Hier', 'unread' => true],
                ['title' => 'Rappel de séance', 'detail' => 'Mathématiques lundi à 17:00', 'time' => 'Hier', 'unread' => false],
            ],
        ];
    }
}
