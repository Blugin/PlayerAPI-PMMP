<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\module;

use pocketmine\Player;

class SetScaleModule extends ModuleCommand{
    use FloatDataTrait;

    public const DEFAULT_KEY = 'default-scale';
    public const TAG_NAME = 'Scale';

    public function apply(Player $player){
        $player->setScale($this->get($player->getLowerCaseName()));
    }
}