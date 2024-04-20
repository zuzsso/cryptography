<?php

declare(strict_types=1);

namespace Cryptography\Tests\Type;

use Cryptography\Random\Exception\InadequateTokenLengthException;
use Cryptography\Random\Exception\TokenNotCompatibleWithCharacterPoolException;
use Cryptography\Random\Type\AbstractCrypToken;
use Cryptography\Random\Type\CharacterPool\AbstractCharacterPool;
use Cryptography\Random\Type\CharacterPool\AlphanumericCaseSensitive;
use Cryptography\Random\Type\CharacterPool\HexadecimalLowerCaseCharacterPool;
use Cryptography\Random\Type\CharacterPool\HexadecimalUpperCaseCharacterPool;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AbstractCrypTokenTest extends TestCase
{
    public function testGeneratesNewTokenIfNoneProvided(): void
    {
        $generated = [];

        $iterations = 10;

        for ($i = 0; $i < $iterations; $i++) {
            $sut = $this->getParametrizedSut(new AlphanumericCaseSensitive(), 32, null);
            $generated [] = $sut->getCryptokenAsString();
        }

        $actual = array_unique($generated);
        self::assertCount($iterations, $actual);
    }

    public function acceptsStringTokenIfMeetsRules(): array
    {
        $alphanumericCharacterPool = new AlphanumericCaseSensitive();
        $hexadecimalLowerCase = new HexadecimalLowerCaseCharacterPool();
        $hexadecimalUpperCase = new HexadecimalUpperCaseCharacterPool();
        return [
            [$alphanumericCharacterPool, 10, '012345Aabc'],
            [$alphanumericCharacterPool, 12, '345AabcAb9zz'],
            [$hexadecimalLowerCase, 3, '0ab'],
            [$hexadecimalUpperCase, 4, '0ABC']
        ];
    }

    /**
     * @dataProvider acceptsStringTokenIfMeetsRules
     */
    public function testAcceptsStringTokenIfItMeetsRules(
        AbstractCharacterPool $characterPool,
        int $length,
        string $token
    ): void {
        self::assertNotNull($this->getParametrizedSut($characterPool, $length, $token));
    }

    public function rejectsTokenIfItDoesntMeetTheRulesDataProvider(): array
    {
        $alphanumericCharacterPool = new AlphanumericCaseSensitive();
        $hexadecimalLowerCase = new HexadecimalLowerCaseCharacterPool();
        $hexadecimalUpperCase = new HexadecimalUpperCaseCharacterPool();
        $emptyCharacterPool = new class () extends AbstractCharacterPool {
        };

        return [
            [$alphanumericCharacterPool, -1, 'abcd', InvalidArgumentException::class, 'The current implementation only generates random strings of length [1, 9223372036854775807], but requested -1'],
            [$alphanumericCharacterPool, 0, 'abcd', InvalidArgumentException::class, 'The current implementation only generates random strings of length [1, 9223372036854775807], but requested 0'],
            [$emptyCharacterPool, 3, 'abc', InvalidArgumentException::class, 'Character pool not big enough'],
            [$hexadecimalUpperCase, 4, 'ABC', InadequateTokenLengthException::class, "This token is required to be of 4 chars long, but got 3: 'ABC'"],
            [$hexadecimalLowerCase, 4, 'mnop', TokenNotCompatibleWithCharacterPoolException::class, "This token contains character outside the allowed character pool. Make sure it only contains characters from this list: '0123456789abcdef'. Given: 'mnop'"],
        ];
    }

    /**
     * @dataProvider rejectsTokenIfItDoesntMeetTheRulesDataProvider
     */
    public function testRejectsTokenIfItDoesntMeetTheRules(
        AbstractCharacterPool $characterPool,
        int $tokenLengthInOneByteChars,
        string $token,
        string $exceptionClass,
        string $exceptionMessage
    ): void {
        $this->expectExceptionMessage($exceptionMessage);
        $this->expectException($exceptionClass);
        $this->getParametrizedSut($characterPool, $tokenLengthInOneByteChars, $token);
    }

    private function getParametrizedSut(
        AbstractCharacterPool $characterPool,
        int $tokenLengthInOneByteChars,
        ?string $token
    ): AbstractCrypToken {
        return new class ($characterPool, $tokenLengthInOneByteChars, $token) extends AbstractCrypToken {
            private int $tokenLengthInOneByteChars;
            private AbstractCharacterPool $characterPool;

            /**
             * @inheritDoc
             */
            public function __construct(
                AbstractCharacterPool $characterPool,
                int $tokenLengthInOneByteChars,
                ?string $crypToken
            ) {
                $this->characterPool = $characterPool;
                $this->tokenLengthInOneByteChars = $tokenLengthInOneByteChars;
                parent::__construct($crypToken);

            }

            public function getCharacterPool(): AbstractCharacterPool
            {
                return $this->characterPool;
            }

            public function getTokenLengthInOneByteChars(): int
            {
                return $this->tokenLengthInOneByteChars;
            }
        };
    }
}
