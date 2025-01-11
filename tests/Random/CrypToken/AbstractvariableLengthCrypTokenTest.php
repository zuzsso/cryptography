<?php

declare(strict_types=1);

namespace Cryptography\Tests\Random\CrypToken;

use Cryptography\Random\Exception\InadequateTokenLengthException;
use Cryptography\Random\Exception\TokenNotCompatibleWithCharacterPoolException;
use Cryptography\Random\Type\CharacterPool\AbstractCharacterPool;
use Cryptography\Random\Type\CharacterPool\AlphanumericCaseSensitive;
use Cryptography\Random\Type\CharacterPool\HexadecimalUpperCaseCharacterPool;
use Cryptography\Random\Type\CharacterPool\KpceCharacterPool;
use Cryptography\Random\Type\CharacterPool\NumbersCharacterPool;
use Cryptography\Random\Type\CrypToken\AbstractVariableLengthCrypToken;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class AbstractvariableLengthCrypTokenTest extends TestCase
{
    public function throwsExceptionIfInvalidTokenLengthDataProvider(): array
    {
        $cp = new KpceCharacterPool();
        $e1 = InvalidArgumentException::class;

        $m1 = 'Minimum length should be 1';
        $m2 = "Min and max lengths are the same. Perhaps you want to use a Fixed Length crypt token";
        $m3 = "Minimum length should be 1";
        return [
            [$cp, -1, 0, $e1, $m1],
            [$cp, -2, -1, $e1, $m1],
            [$cp, 0, 0, $e1, $m2],
            [$cp, 1, 1, $e1, $m2],
            [$cp, 100, 100, $e1, $m2],
            [$cp, 0, 1, $e1, $m3],
        ];
    }

    /**
     * @throws ReflectionException
     * @dataProvider throwsExceptionIfInvalidTokenLengthDataProvider
     */
    public function testThrowsExceptionIfInvalidTokenLength(
        AbstractCharacterPool $characterPool,
        int $minLength,
        int $maxLength,
        string $expectedExceptionClass,
        string $expectedExceptionMessage

    ): void {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->getMeACustomSutPleaseThankYouEverSoMuch($characterPool, $minLength, $maxLength);
    }

    public function shouldGenerateTokenOfGivenLengthDataProvider(): array
    {
        $cp = new NumbersCharacterPool();
        return [
            [$cp, 1, 2],
            [$cp, 20, 30],
            [$cp, 100, 101],
            [$cp, 123, 200],
        ];
    }

    /**
     * @throws ReflectionException
     * @dataProvider shouldGenerateTokenOfGivenLengthDataProvider
     */
    public function testShouldGenerateTokenOfGivenLength(
        AbstractCharacterPool $cp,
        int $minLength,
        int $maxLength
    ): void {
        $sut = $this->getMeACustomSutPleaseThankYouEverSoMuch($cp, $minLength, $maxLength);

        $token = $sut->getCryptokenAsString();

        $thisTokenLength = strlen($token);

        self::assertTrue(
            $minLength <= $thisTokenLength,
            "Failed assertion. $minLength is not >= than $thisTokenLength"
        );

        self::assertTrue(
            $thisTokenLength <= $maxLength,
            "Failed assertion. $thisTokenLength is not <= than $thisTokenLength"
        );

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

        $ms1 = "The length of the provided token is out of specs: 4 chars. Required: [1, 3] chars";
        $ms2 = "The length of the provided token is out of specs: 2 chars. Required: [4, 7] chars";
        $ms3 =
            "This token contains character outside the allowed character pool. Make sure it only contains characters from this list: '0123456789'. Given: '12a3'";
        $ms4 =
            "This token contains character outside the allowed character pool. Make sure it only contains characters from this list: '0123456789'. Given: '012A3'";
        $ms5 =
            "This token contains character outside the allowed character pool. Make sure it only contains characters from this list: '0123456789'. Given: '1203aA'";
        return [
            // Expected token length different from the length of the provided string
            [$cp, 1, 3, '1245', $ec1, $ms1],
            [$cp, 4, 7, '12', $ec1, $ms2],
            //
            // Correct length, but wrong chars
            [$cp, 1, 10, '12a3', $ec2, $ms3],
            [$cp, 3, 6, '012A3', $ec2, $ms4],
            [$cp, 5, 100, '1203aA', $ec2, $ms5],
        ];
    }

    /**
     * @dataProvider throwsExceptionIfInitializingWithIncompatibleStringDataProvider
     * @throws ReflectionException
     */
    public function testThrowsExceptionIfInitializingWithIncompatibleString(
        AbstractCharacterPool $cp,
        int $tokenMinLength,
        int $tokenMaxLength,
        string $initialToken,
        string $expectedExceptionClass,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->getMeACustomSutPleaseThankYouEverSoMuch($cp, $tokenMinLength, $tokenMaxLength, $initialToken);
    }

    public function shouldInitializeCorrectlyIfCompatibleTokenProvidedDataProvider(): array
    {
        $cp1 = new NumbersCharacterPool();
        $cp2 = new AlphanumericCaseSensitive();
        $cp3 = new HexadecimalUpperCaseCharacterPool();

        return [

            // Correct length, but wrong chars
            [$cp1, 1, 10, '1234'],
            [$cp1, 1, 10, '56789'],
            [$cp1, 5, 15, '120319'],

            [$cp2, 3, 20, 'Aabc123'],
            [$cp2, 10, 20, 'Aabc123PzZ'],

            [$cp3, 3, 7, '1AB2F']
        ];
    }

    /**
     * @throws ReflectionException
     * @dataProvider shouldInitializeCorrectlyIfCompatibleTokenProvidedDataProvider
     */
    public function testShouldInitializeCorrectlyIfCompatibleTokenProvided(
        AbstractCharacterPool $cp,
        int $minLength,
        int $maxLength,
        string $initialToken
    ): void {
        $sut = $this->getMeACustomSutPleaseThankYouEverSoMuch($cp, $minLength, $maxLength, $initialToken);

        self::assertEquals($sut->getCryptokenAsString(), $initialToken);
    }

    /**
     * @throws ReflectionException
     */
    private function getMeACustomSutPleaseThankYouEverSoMuch(
        AbstractCharacterPool $characterPool,
        int $minLength,
        int $maxLength,
        ?string $token = null
    ) {
        $result = $this->getMockForAbstractClass(AbstractVariableLengthCrypToken::class, [], '', false);

        $result->method('getCharacterPool')->willReturn($characterPool);
        $result->method('getTokenMinLengthInOneByteChars')->willReturn($minLength);
        $result->method('getTokenMaxLengthInOneByteChars')->willReturn($maxLength);

        $reflectionClass = new ReflectionClass(AbstractVariableLengthCrypToken::class);
        $constructor = $reflectionClass->getConstructor();

        if ($token === null) {
            $constructor->invoke($result);
        } else {
            $constructor->invoke($result, $token);
        }

        return $result;
    }
}