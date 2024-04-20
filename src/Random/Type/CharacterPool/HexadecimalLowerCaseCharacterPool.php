<?php

declare(strict_types=1);

namespace Cryptography\Random\Type\CharacterPool;

class HexadecimalLowerCaseCharacterPool extends AbstractCharacterPool
{
    /**
     * @noinspection DuplicatedCode
     */
    public function __construct()
    {
        $this->addCharacterToPool("0");
        $this->addCharacterToPool("1");
        $this->addCharacterToPool("2");
        $this->addCharacterToPool("3");
        $this->addCharacterToPool("4");
        $this->addCharacterToPool("5");
        $this->addCharacterToPool("6");
        $this->addCharacterToPool("7");
        $this->addCharacterToPool("8");
        $this->addCharacterToPool("9");
        $this->addCharacterToPool("a");
        $this->addCharacterToPool("b");
        $this->addCharacterToPool("c");
        $this->addCharacterToPool("d");
        $this->addCharacterToPool("e");
        $this->addCharacterToPool("f");
    }
}
