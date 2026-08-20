<?php

namespace Tests\Unit;

use App\Support\SubjectAbbreviation;
use PHPUnit\Framework\TestCase;

class SubjectAbbreviationTest extends TestCase
{
    public function test_it_abbreviates_known_subjects(): void
    {
        $this->assertSame('Math, Ang', SubjectAbbreviation::display('Mathématiques, Anglais'));
        $this->assertSame('PC, Fr, HG', SubjectAbbreviation::display('Physique-Chimie, Français, Histoire-Géographie'));
        $this->assertSame('Info, Ar, SVT', SubjectAbbreviation::display('Informatique / Arabe ; SVT'));
    }

    public function test_it_keeps_unknown_labels(): void
    {
        $this->assertSame('Philosophie', SubjectAbbreviation::display('Philosophie'));
        $this->assertSame('—', SubjectAbbreviation::display(''));
    }
}
