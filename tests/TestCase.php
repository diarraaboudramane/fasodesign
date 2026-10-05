<?php

namespace Tests;

use App\Http\Middleware\VerifierHumain;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /* Par défaut, le visiteur des essais a déjà coché la case : chaque
           essai vérifie sa page, pas la barrière. VerificationTest retire
           ce cookie pour éprouver la barrière elle-même. */
        $this->withCookie(VerifierHumain::COOKIE, (string) time());
    }
}
