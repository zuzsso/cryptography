<?php

declare(strict_types=1);

namespace Cryptography\Tests\Random\Mocks;

use Cryptography\Random\Type\CharacterPool\AbstractCharacterPool;

class GenericCharacterPoolMock extends AbstractCharacterPool
{
    public function __construct(string $chars)
    {
        $stringLength = strlen($chars);

        for ($i = 0; $i < $stringLength; $i++) {
            $this->addCharacterToPool($chars[$i]);
        }
    }
}
