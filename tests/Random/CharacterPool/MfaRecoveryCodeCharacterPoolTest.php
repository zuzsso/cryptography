<?php

declare(strict_types=1);

namespace Cryptography\Tests\Random\CharacterPool;

use PHPUnit\Framework\TestCase;
use Cryptography\Random\Type\CharacterPool\AbstractCharacterPool;
use Cryptography\Random\Type\CharacterPool\MfaRecoveryCodeCharacterPool;

class MfaRecoveryCodeCharacterPoolTest extends TestCase
{
    private MfaRecoveryCodeCharacterPool $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new MfaRecoveryCodeCharacterPool();
    }

    public function testHasExpectedCharacters(): void
    {
        $actual = $this->sut->getCharacterPoolAsSingleString();
        self::assertEquals('23456789BCDFGHJKLMNPQRSTVWXYZ', $actual);
    }

    public function testExtendsExpectedClass(): void
    {
        self::assertInstanceOf(AbstractCharacterPool::class, $this->sut);
    }
}
