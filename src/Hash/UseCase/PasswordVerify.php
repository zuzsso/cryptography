<?php

declare(strict_types=1);

namespace Cryptography\Hash\UseCase;

use Cryptography\Hash\Exception\HashServiceUnmanagedException;

interface PasswordVerify
{
    /**
     * @throws HashServiceUnmanagedException
     */
    public function verifyPassword(string $hash, string $clearTextPassword): bool;
}
