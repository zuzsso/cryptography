<?php

declare(strict_types=1);

namespace Cryptography;

use Cryptography\Hash\Service\HashService;
use Cryptography\Hash\UseCase\GenerateStringHash;
use Cryptography\Hash\UseCase\PasswordHash;
use Cryptography\Hash\UseCase\PasswordVerify;
use DiManifest\AbstractDependencyInjection;

use function DI\autowire;

class DependencyInjectionManifest extends AbstractDependencyInjection
{
    public static function getDependencies(): array
    {
        return [
            GenerateStringHash::class => autowire(HashService::class),
            PasswordHash::class => autowire(HashService::class),
            PasswordVerify::class => autowire(HashService::class)
        ];
    }
}
