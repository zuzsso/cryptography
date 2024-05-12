<?php

declare(strict_types=1);

namespace Cryptography\Tests\Random\CharacterPool;

use Cryptography\Tests\Random\Mocks\GenericCharacterPoolMock;
use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Cryptography\Random\Type\CharacterPool\AbstractCharacterPool;

class AbstractCharacterPoolTest extends TestCase
{
    /**
     * @var AbstractCharacterPool | MockObject
     */
    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = $this->getMockForAbstractClass(AbstractCharacterPool::class);
    }


    public function throwsExceptionIfIncorrectCharPoolConfig(): array
    {
        $msg1 =
            "This current implementation doesn't support multi char strings, empty strings " .
            "or multi-byte encoded chars in the character pool";

        $msg2 = "Character 'a' already exists in the pool";

        return [
            [[''], InvalidArgumentException::class, $msg1],
            [['test'], InvalidArgumentException::class, $msg1],
            [['😇'], InvalidArgumentException::class, $msg1],
            [['a', 'b', 'a'], InvalidArgumentException::class, $msg2],
        ];
    }

    /**
     * @throws ReflectionException
     * @dataProvider throwsExceptionIfIncorrectCharPoolConfig
     */
    public function testThrowsExceptionIfRepeatedCharInPool(
        array $charsFixture,
        string $expectedExceptionClass,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        foreach ($charsFixture as $char) {
            self::callAddCharacterToPool($this->sut, [$char]);
        }
    }

    public function returnsCorrectCharPoolSizeDataProvider(): array
    {
        return [
            [[], 0],
            [['a'], 1],
            [['a', 'b'], 2]
        ];
    }

    /**
     * @throws ReflectionException
     * @dataProvider returnsCorrectCharPoolSizeDataProvider
     */
    public function testReturnsCorrectCharPoolSize(array $charsFixture, int $expected): void
    {
        foreach ($charsFixture as $char) {
            self::callAddCharacterToPool($this->sut, [$char]);
        }

        $actual = $this->sut->characterPoolSize();

        self::assertEquals($expected, $actual);
    }

    /**
     * @throws ReflectionException
     */
    public function testCharacterPoolIsCaseSensitive(): void
    {
        self::callAddCharacterToPool($this->sut, ['a']);
        self::callAddCharacterToPool($this->sut, ['A']);

        $actual = $this->sut->getCharacterPoolAsSingleString();
        self::assertEquals('aA', $actual);
    }

    /**
     * @throws ReflectionException
     */
    public function testCorrectlyGetsTheCharPoolAsString(): void
    {
        $charsFixture = ['a', 'b', 'c'];

        foreach ($charsFixture as $char) {
            self::callAddCharacterToPool($this->sut, [$char]);
        }

        $actual = $this->sut->getCharacterPoolAsSingleString();

        self::assertEquals('abc', $actual);
    }

    public function throwsExceptionIfGettingCharOutsideCollectionLimitsDataProvider(): array
    {
        $charset = ['a', 'b', 'c'];

        $e = OutOfBoundsException::class;

        return [
            [$charset, -1, $e, "Position -1 outside of interval [0, 2]"],
            [$charset, 3, $e, "Position 3 outside of interval [0, 2]"]
        ];
    }

    /**
     * @param array $charsetFixture
     * @param int $position
     * @param string $expectedExceptionClass
     * @param string $expectedExceptionMessage
     * @throws ReflectionException
     * @dataProvider throwsExceptionIfGettingCharOutsideCollectionLimitsDataProvider
     */
    public function testThrowsExceptionIfGettingCharOutsideCollectionLimits(
        array $charsetFixture,
        int $position,
        string $expectedExceptionClass,
        string $expectedExceptionMessage
    ): void {
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->expectException($expectedExceptionClass);
        foreach ($charsetFixture as $char) {
            self::callAddCharacterToPool($this->sut, [$char]);
        }

        $this->sut->getCharAt($position);
    }

    /**
     * @throws ReflectionException
     */
    public function testCorrectlyRetrievesCharAt(): void
    {
        self::callAddCharacterToPool($this->sut, ['a']);
        self::callAddCharacterToPool($this->sut, ['b']);

        self::assertEquals('a', $this->sut->getCharAt(0));
        self::assertEquals('b', $this->sut->getCharAt(1));
    }

    public function correctlyChecksIfStringIsCompatibleWithCharacterPoolDataProvider(): array
    {
        return [
            ['A', true],
            ['AA', true],
            ['AbAbAb', true],
            ['AbCdAbCd', true],
            ['a', false],
            ['aBcD', false],
            ['!', false]
        ];
    }

    /**
     * @dataProvider correctlyChecksIfStringIsCompatibleWithCharacterPoolDataProvider
     */
    public function testCorrectlyChecksIfStringIsCompatibleWithCharacterPool(string $s, bool $expected): void
    {
        $sut = new GenericCharacterPoolMock('AbCd');

        $actual = $sut->checkStringIsCompatibleWithCharacterPool($s);

        self::assertEquals($expected, $actual);
    }

    /**
     * @throws ReflectionException
     */
    private static function callAddCharacterToPool(AbstractCharacterPool $obj, array $args): void
    {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod('addCharacterToPool');
        $method->setAccessible(true);
        $method->invokeArgs($obj, $args);
    }
}
