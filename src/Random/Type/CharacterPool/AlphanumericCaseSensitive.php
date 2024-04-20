<?php

declare(strict_types=1);

namespace Cryptography\Random\Type\CharacterPool;

class AlphanumericCaseSensitive extends AbstractCharacterPool
{
    public function __construct()
    {
        /** @noinspection DuplicatedCode */
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
        $this->addCharacterToPool("g");
        $this->addCharacterToPool("h");
        $this->addCharacterToPool("i");
        $this->addCharacterToPool("j");
        $this->addCharacterToPool("k");
        $this->addCharacterToPool("l");
        $this->addCharacterToPool("m");
        $this->addCharacterToPool("n");
        $this->addCharacterToPool("o");
        $this->addCharacterToPool("p");
        $this->addCharacterToPool("q");
        $this->addCharacterToPool("r");
        $this->addCharacterToPool("s");
        $this->addCharacterToPool("t");
        $this->addCharacterToPool("u");
        $this->addCharacterToPool("v");
        $this->addCharacterToPool("w");
        $this->addCharacterToPool("x");
        $this->addCharacterToPool("y");
        $this->addCharacterToPool("z");

        $this->addCharacterToPool("A");
        $this->addCharacterToPool("B");
        $this->addCharacterToPool("C");
        $this->addCharacterToPool("D");
        $this->addCharacterToPool("E");
        $this->addCharacterToPool("F");
        $this->addCharacterToPool("G");
        $this->addCharacterToPool("H");
        $this->addCharacterToPool("I");
        $this->addCharacterToPool("J");
        $this->addCharacterToPool("K");
        $this->addCharacterToPool("L");
        $this->addCharacterToPool("M");
        $this->addCharacterToPool("N");
        $this->addCharacterToPool("O");
        $this->addCharacterToPool("P");
        $this->addCharacterToPool("Q");
        $this->addCharacterToPool("R");
        $this->addCharacterToPool("S");
        $this->addCharacterToPool("T");
        $this->addCharacterToPool("U");
        $this->addCharacterToPool("V");
        $this->addCharacterToPool("W");
        $this->addCharacterToPool("X");
        $this->addCharacterToPool("Y");
        $this->addCharacterToPool("Z");
    }
}
