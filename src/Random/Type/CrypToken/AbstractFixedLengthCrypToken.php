<?php

declare(strict_types=1);

namespace Cryptography\Random\Type\CrypToken;

use Cryptography\Random\Exception\InadequateTokenLengthException;
use InvalidArgumentException;

abstract class AbstractFixedLengthCrypToken extends AbstractCrypToken
{
    /**
     * @inheritDoc
     */
    final protected function validateTokenLengthConfiguration(): void
    {
        $tokenLengthInOneByteChars = $this->getExpectedTokenLengthInOneByteChars();

        if ($tokenLengthInOneByteChars < 1) {
            throw new InvalidArgumentException(
                "The current implementation only generates random strings of length [1, " .
                PHP_INT_MAX . "], but requested $tokenLengthInOneByteChars"
            );
        }
    }

    /**
     * @throws InadequateTokenLengthException
     */
    final protected function checkProvidedTokenLength(string $crypToken): void
    {
        $expectedTokenLength = $this->getExpectedTokenLengthInOneByteChars();
        $a = strlen($crypToken);

        if ($a !== $expectedTokenLength) {
            throw new InadequateTokenLengthException(
                "This token is required to be of $expectedTokenLength chars long, but got $a: '$crypToken'"
            );
        }
    }
}
