<?php

declare(strict_types=1);

namespace Cryptography\Random\Type\CharacterPool;

/**
 * Proof Key of Code Exchange (KPCE)
 * @see https://oauth.net/2/pkce/
 */
class KpceCharacterPool extends AlphanumericCaseSensitive
{
    public function __construct()
    {
        parent::__construct();
        $this->addCharacterToPool('-');
        $this->addCharacterToPool('_');
        $this->addCharacterToPool('~');
        $this->addCharacterToPool('.');
    }
}