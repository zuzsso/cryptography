<?php

declare(strict_types=1);

namespace Cryptography\Tests\Random\CrypToken;

use Cryptography\Random\Exception\InadequateTokenLengthException;
use Cryptography\Random\Exception\TokenNotCompatibleWithCharacterPoolException;
use Cryptography\Random\Type\CharacterPool\AbstractCharacterPool;
use Cryptography\Random\Type\CharacterPool\AlphanumericCaseSensitive;
use Cryptography\Random\Type\CharacterPool\HexadecimalUpperCaseCharacterPool;
use Cryptography\Random\Type\CharacterPool\NumbersCharacterPool;
use Cryptography\Random\Type\CrypToken\AbstractFixedLengthCrypToken;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class AbstractFixedLengthCrypTokenTest extends TestCase
{
    public function throwsExceptionIfInvalidLengthDataProvider(): array
    {
        $cp = new NumbersCharacterPool();
        return [
            [$cp, 0],
            [$cp, -1],
        ];
    }

    /**
     * @throws ReflectionException
     * @dataProvider throwsExceptionIfInvalidLengthDataProvider
     */
    public function testThrowsExceptionIfInvalidLength(AbstractCharacterPool $characterPool, int $length): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The current implementation only generates random strings of length [1, 9223372036854775807], but requested ' .
            $length
        );

        $this->getMeACustomSutPleaseThankYouEverSoMuch($characterPool, $length);
    }

    public function shouldGenerateTokenOfGivenLengthDataProvider(): array
    {
        $cp = new NumbersCharacterPool();
        return [
            [$cp, 1],
            [$cp, 2],
            [$cp, 20],
            [$cp, 123],
        ];
    }

    /**
     * @throws ReflectionException
     * @dataProvider shouldGenerateTokenOfGivenLengthDataProvider
     */
    public function testShouldGenerateTokenOfGivenLength(AbstractCharacterPool $cp, int $lenth): void
    {
        $sut = $this->getMeACustomSutPleaseThankYouEverSoMuch($cp, $lenth);

        $token = $sut->getCryptokenAsString();

        self::assertEquals($lenth, strlen($token));

        $cp = $sut->getCharacterPool();

        $tokenLength = strlen($token);

        for ($i = 0; $i < $tokenLength; $i++) {
            self::assertTrue($cp->charInCharacterPool($token[$i]));
        }
    }

    public function throwsExceptionIfInitializingWithIncompatibleStringDataProvider(): array
    {
        $cp = new NumbersCharacterPool();
        $ec1 = InadequateTokenLengthException::class;
        $ec2 = TokenNotCompatibleWithCharacterPoolException::class;

        $ms1 = "This token is required to be of 1 chars long, but got 2: '12'";
        $ms2 = "This token is required to be of 4 chars long, but got 2: '12'";
        $ms3 =
            "This token contains character outside the allowed character pool. Make sure it only contains characters from this list: '0123456789'. Given: '12a3'";
        $ms4 =
            "This token contains character outside the allowed character pool. Make sure it only contains characters from this list: '0123456789'. Given: '012A3'";
        $ms5 =
            "This token contains character outside the allowed character pool. Make sure it only contains characters from this list: '0123456789'. Given: '1203aA'";
        return [
            // Expected token length different from the length of the provided string
            [$cp, 1, '12', $ec1, $ms1],
            [$cp, 4, '12', $ec1, $ms2],

            // Correct length, but wrong chars
            [$cp, 4, '12a3', $ec2, $ms3],
            [$cp, 5, '012A3', $ec2, $ms4],
            [$cp, 6, '1203aA', $ec2, $ms5],
        ];
    }

    /**
     * @dataProvider throwsExceptionIfInitializingWithIncompatibleStringDataProvider
     * @throws ReflectionException
     */
    public function testThrowsExceptionIfInitializingWithIncompatibleString(
        AbstractCharacterPool $cp,
        int $tokenLength,
        string $initialToken,
        string $expectedExceptionClass,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->getMeACustomSutPleaseThankYouEverSoMuch($cp, $tokenLength, $initialToken);
    }

    public function shouldInitializeCorrectlyIfCompatibleTokenProvidedDataProvider(): array
    {
        $cp1 = new NumbersCharacterPool();
        $cp2 = new AlphanumericCaseSensitive();
        $cp3 = new HexadecimalUpperCaseCharacterPool();

        return [

            // Correct length, but wrong chars
            [$cp1, 4, '1234'],
            [$cp1, 5, '56789'],
            [$cp1, 6, '120319'],

            [$cp2, 7, 'Aabc123'],
            [$cp2, 10, 'Aabc123PzZ'],

            [$cp3, 5, '1AB2F']
        ];
    }

    /**
     * @throws ReflectionException
     * @dataProvider shouldInitializeCorrectlyIfCompatibleTokenProvidedDataProvider
     */
    public function testShouldInitializeCorrectlyIfCompatibleTokenProvided(
        AbstractCharacterPool $cp,
        int $tokenLength,
        string $initialToken
    ): void {
        $sut = $this->getMeACustomSutPleaseThankYouEverSoMuch($cp, $tokenLength, $initialToken);

        $thisTokenLength = $sut->getCryptokenAsString();

        self::assertEquals($tokenLength, strlen($thisTokenLength));
    }

    /**
     * @throws ReflectionException
     */
    private function getMeACustomSutPleaseThankYouEverSoMuch(
        AbstractCharacterPool $characterPool,
        int $length,
        ?string $token = null
    ) {
        $result = $this->getMockForAbstractClass(AbstractFixedLengthCrypToken::class, [], '', false);

        $result->method('getCharacterPool')->willReturn($characterPool);
        $result->method('getExpectedTokenLengthInOneByteChars')->willReturn($length);

        $reflectionClass = new ReflectionClass(AbstractFixedLengthCrypToken::class);
        $constructor = $reflectionClass->getConstructor();

        if ($token === null) {
            $constructor->invoke($result);
        } else {
            $constructor->invoke($result, $token);
        }

        return $result;
    }
}
