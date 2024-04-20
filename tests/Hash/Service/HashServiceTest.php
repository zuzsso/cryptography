<?php

declare(strict_types=1);

namespace Cryptography\Tests\Hash\Service;

use Cryptography\Tests\CustomTestCase;
use SodiumException;
use Cryptography\Hash\Service\HashService;

class HashServiceTest extends CustomTestCase
{
    private HashService $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new HashService();
    }

    /**
     * @throws SodiumException
     */
    public function testHashesCorrectly(): void
    {
        $hash = $this->sut->passwordHash('me testing');

        self::assertStringContainsString('$argon2id$v=19$m=65536,t=2,p=', $hash);

    }

    /**
     * @throws SodiumException
     */
    public function testVerifiesCorrectly(): void
    {
        $storedHash = '$argon2id$v=19$m=65536,t=2,p=1$wxPZ7GMy466cijsVYDJexw$FCn5ZBcDHh05gMWMIWRjIpykFS7TGPr2JEUj73AOXco';

        self::assertTrue($this->sut->verifyPassword($storedHash, 'me testing'));

        self::assertFalse($this->sut->verifyPassword($storedHash . 'tampered', 'me testing'));
    }
}
