<?php

declare(strict_types=1);

namespace Cryptography\Hash\UseCase;

interface GenerateStringHash
{
    public function useSha256HexOutput(string $clearTextString): string;
}
