<?php

namespace Tests\Unit;

use App\Support\EcopiloteIdentity;
use PHPUnit\Framework\TestCase;

class EcopiloteIdentityTest extends TestCase
{
    public function test_it_appends_the_default_domain(): void
    {
        $this->assertSame('zerragui@ecopilote.ma', EcopiloteIdentity::email('zerragui'));
        $this->assertSame('etudiant@ecopilote.ma', EcopiloteIdentity::email('etudiant'));
    }

    public function test_it_keeps_an_existing_email_intact(): void
    {
        $this->assertSame('zerragui@ecopilote.ma', EcopiloteIdentity::email('zerragui@ecopilote.ma'));
        $this->assertSame('autre@exemple.ma', EcopiloteIdentity::email('autre@exemple.ma'));
    }

    public function test_it_extracts_the_local_part_for_display(): void
    {
        $this->assertSame('zerragui', EcopiloteIdentity::localPart('zerragui@ecopilote.ma'));
        $this->assertSame('zerragui', EcopiloteIdentity::localPart('zerragui'));
        $this->assertSame('@ecopilote.ma', EcopiloteIdentity::emailSuffix());
    }

    public function test_it_builds_a_login_from_a_name(): void
    {
        $this->assertSame(
            'nadia.el.amrani@ecopilote.ma',
            EcopiloteIdentity::loginFromName('Nadia El Amrani')
        );
    }
}
