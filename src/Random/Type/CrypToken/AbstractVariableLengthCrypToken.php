<?php

declare(strict_types=1);

namespace Cryptography\Random\Type\CrypToken;

use Cryptography\Random\Exception\InadequateTokenLengthException;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

abstract class AbstractVariableLengthCrypToken extends AbstractCrypToken
{
    abstract public function getTokenMinLengthInOneByteChars(): int;

    abstract public function getTokenMaxLengthInOneByteChars(): int;

    /**
     * @inheritDoc
     */
    public function getExpectedTokenLengthInOneByteChars(): int
    {
        $minLength = $this->getTokenMinLengthInOneByteChars();
        $maxLength = $this->getTokenMaxLengthInOneByteChars();

        try {
            if ($this->crypToken === null) {
                return random_int($minLength, $maxLength);
            }
        } catch (Throwable $t) {
            throw new RuntimeException($t->getMessage(), $t->getCode(), $t);
        }

        $this->checkProvidedTokenLength($this->crypToken);

        return strlen($this->crypToken);
    }

    final protected function validateTokenLengthConfiguration(): void
    {
        $min = $this->getTokenMinLengthInOneByteChars();
        $max = $this->getTokenMaxLengthInOneByteChars();

        if ($min === $max) {
            throw new InvalidArgumentException(
                "Min and max lengths are the same. Perhaps you want to use a Fixed Length crypt token"
            );
        }

        if ($min > $max) {
            throw new InvalidArgumentException(
                "Variable crypt token lenght not properly defined: Min: $min chars, Max: $max chars"
            );
        }

        if ($min <= 0) {
            throw new InvalidArgumentException("Minimum length should be 1");
        }
    }

    /**
     * @throws InadequateTokenLengthException
     */
    final protected function checkProvidedTokenLength(string $crypToken): void
    {
        $minLength = $this->getTokenMinLengthInOneByteChars();
        $maxLength = $this->getTokenMaxLengthInOneByteChars();

        // Cryptoken is not null, so we need to check that the length is within specs
        $tokenLength = strlen($crypToken);

        $withinRange = ($minLength <= $tokenLength) && ($tokenLength <= $maxLength);

        if (!$withinRange) {
            throw new InadequateTokenLengthException(
                "The length of the provided token is out of specs: $tokenLength chars. Required: [$minLength, $maxLength] chars"
            );
        }
    }
}
