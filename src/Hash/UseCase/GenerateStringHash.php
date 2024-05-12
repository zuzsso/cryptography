<?php

declare(strict_types=1);

namespace Cryptography\Hash\UseCase;

use Cryptography\Hash\Exception\HashServiceUnmanagedException;

interface GenerateStringHash
{
    /**
     * @throws HashServiceUnmanagedException
     */
    public function useSha256HexOutput(string $clearTextString): string;

    /**
     * @throws HashServiceUnmanagedException
     */
    public function useSha256BinaryOutput(string $clearTextString): string;

    /**
     * @throws HashServiceUnmanagedException
     */
    public function useCrc32cHexOutput(string $clearTextString): string;

    /**
     * @throws HashServiceUnmanagedException
     */
    public function useCrc32cbinaryOutput(string $clearTextString): string;
}
