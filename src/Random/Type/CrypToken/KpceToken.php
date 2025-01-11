<?php

declare(strict_types=1);

namespace Cryptography\Random\Type\CrypToken;

use Cryptography\Random\Type\CharacterPool\AbstractCharacterPool;
use Cryptography\Random\Type\CharacterPool\KpceCharacterPool;

/**
 * @see https://oauth.net/2/pkce/
 * @see https://datatracker.ietf.org/doc/html/rfc7636
 * @see https://www.authlete.com/developers/pkce/
 */
class KpceToken extends AbstractVariableLengthCrypToken
{
    public function getCharacterPool(): AbstractCharacterPool
    {
        return new KpceCharacterPool();
    }

    public function getTokenMinLengthInOneByteChars(): int
    {
        return 43;
    }

    public function getTokenMaxLengthInOneByteChars(): int
    {
        return 128;
    }
}
