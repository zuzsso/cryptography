<?php

declare(strict_types=1);

namespace Cryptography\Random\Type\CrypToken;

use Cryptography\Random\Exception\InadequateTokenLengthException;
use Cryptography\Random\Exception\TokenNotCompatibleWithCharacterPoolException;
use Cryptography\Random\Exception\UnableToGenerateRandomTokenUnmanagedException;
use Cryptography\Random\Type\CharacterPool\AbstractCharacterPool;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

abstract class AbstractCrypToken
{
    protected ?string $crypToken = null;

    abstract public function getCharacterPool(): AbstractCharacterPool;

    /**
     * @throws InadequateTokenLengthException
     */
    abstract protected function validateTokenLengthConfiguration(): void;

    /**
     * @throws InadequateTokenLengthException
     */
    abstract public function getExpectedTokenLengthInOneByteChars(): int;

    abstract protected function checkProvidedTokenLength(string $crypToken): void;

    /**
     * @throws TokenNotCompatibleWithCharacterPoolException
     * @throws UnableToGenerateRandomTokenUnmanagedException
     * @throws InadequateTokenLengthException
     */
    public function __construct(?string $crypToken = null)
    {
        if ($crypToken !== null) {
            $this->crypToken = $crypToken;
        }

        $characterPool = $this->getCharacterPool();
        $tokenLengthInOneByteChars = $this->getExpectedTokenLengthInOneByteChars();

        $this->validateCharacterPool($characterPool);
        $this->validateTokenLengthConfiguration();

        if ($crypToken === null) {
            $this->crypToken = $this->generateNewCryptoken(
                $characterPool,
                $tokenLengthInOneByteChars
            );
        } else {
            $this->crypToken = $crypToken;
            $this->checkProvidedTokenLength($crypToken);
            $this->checkTokenCompatibleWithCharacterPool($characterPool, $this->crypToken);
        }
    }

    final protected function validateCharacterPool(AbstractCharacterPool $characterPool): void
    {
        $poolSize = $characterPool->characterPoolSize();

        if ($poolSize < 1) {
            throw new InvalidArgumentException('Character pool not big enough');
        }
    }

    final public function getCryptokenAsString(): string
    {
        if ($this->crypToken === null) {
            throw new RuntimeException("Unexpected: the token was not yet initialized");
        }
        return $this->crypToken;
    }

    /**
     * @throws UnableToGenerateRandomTokenUnmanagedException
     */
    protected function generateNewCryptoken(
        AbstractCharacterPool $characterPool,
        int $tokenLengthInOneByteChars
    ): string {
        $poolSize = $characterPool->characterPoolSize();

        $result = '';

        while (strlen($result) < $tokenLengthInOneByteChars) {
            try {
                $atRandom = random_int(0, $poolSize - 1);
            } catch (Throwable $e) {
                throw new UnableToGenerateRandomTokenUnmanagedException(
                    "Could not generate a random integer using PHP native functions",
                    $e->getCode(),
                    $e
                );
            }
            $result .= $characterPool->getCharAt($atRandom);
        }

        return $result;
    }

    /**
     * @throws TokenNotCompatibleWithCharacterPoolException
     */
    final protected function checkTokenCompatibleWithCharacterPool(
        AbstractCharacterPool $characterPool,
        string $crypToken
    ): void {
        if (!$characterPool->checkStringIsCompatibleWithCharacterPool($crypToken)) {
            $allowed = $characterPool->getCharacterPoolAsSingleString();
            throw new TokenNotCompatibleWithCharacterPoolException(
                "This token contains character outside the allowed character pool. Make " .
                "sure it only contains characters from this list: '$allowed'. Given: '$crypToken'"
            );
        }
    }
}
