<?php

namespace Tests\Unit;

use App\Support\EcopiloteIdentity;
use PHPUnit\Framework\TestCase;

class EcopiloteIdentityTest extends TestCase
{
    public function test_it_appends_the_default_domain(): void
    {
        $this->assertSame('zerragui@esipres.com', EcopiloteIdentity::email('zerragui'));
        $this->assertSame('etudiant@esipres.com', EcopiloteIdentity::email('etudiant'));
    }

    public function test_it_keeps_an_existing_email_intact(): void
    {
        $this->assertSame('zerragui@esipres.com', EcopiloteIdentity::email('zerragui@esipres.com'));
        $this->assertSame('autre@exemple.ma', EcopiloteIdentity::email('autre@exemple.ma'));
    }

    public function test_it_extracts_the_local_part_for_display(): void
    {
        $this->assertSame('zerragui', EcopiloteIdentity::localPart('zerragui@esipres.com'));
        $this->assertSame('zerragui', EcopiloteIdentity::localPart('zerragui'));
        $this->assertSame('@esipres.com', EcopiloteIdentity::emailSuffix());
    }

    public function test_it_builds_a_login_from_a_name(): void
    {
        $this->assertSame(
            'nadia.el.amrani@esipres.com',
            EcopiloteIdentity::loginFromName('Nadia El Amrani')
        );
    }
}
