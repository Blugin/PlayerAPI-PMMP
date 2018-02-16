<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\module;

use pocketmine\Player;
use pocketmine\entity\Attribute;

class SetSpeedModule extends ModuleCommand{

    use FloatDataTrait;

    public const DEFAULT_KEY = 'default-speed';
    public const TAG_NAME = 'Speed';

    public function apply(Player $player){
        $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue($this->get($player->getLowerCaseName()));
    }
}