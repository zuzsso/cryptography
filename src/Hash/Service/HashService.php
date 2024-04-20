<?php

declare(strict_types=1);

namespace Cryptography\Hash\Service;

use Cryptography\Hash\Exception\HashServiceUnmanagedException;
use Cryptography\Hash\UseCase\PasswordHash;
use Cryptography\Hash\UseCase\PasswordVerify;
use Cryptography\Hash\UseCase\GenerateStringHash;
use Throwable;

class HashService implements PasswordHash, PasswordVerify, GenerateStringHash
{
    /**
     * @inheritDoc
     */
    public function passwordHash(string $clearTextString): string
    {
        try {
            return sodium_crypto_pwhash_str(
                $clearTextString,
                SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
            );
        } catch (Throwable $t) {
            throw new HashServiceUnmanagedException($t->getMessage(), $t->getCode(), $t);
        }
    }

    /**
     * @inheritDoc
     */
    public function verifyPassword(string $hash, string $clearTextPassword): bool
    {
        try {
            return sodium_crypto_pwhash_str_verify($hash, $clearTextPassword);
        } catch (Throwable $t) {
            throw new HashServiceUnmanagedException($t->getMessage(), $t->getCode(), $t);
        }
    }

    public function useSha256HexOutput(string $clearTextString): string
    {
        return hash('sha256', $clearTextString);
    }
}
