<?php

declare(strict_types=1);

namespace Cryptography\Hash\UseCase;

use Cryptography\Hash\Exception\HashServiceUnmanagedException;

interface PasswordHash
{
    /**
     * @throws HashServiceUnmanagedException
     */
    public function passwordHash(string $clearTextString): string;
}
