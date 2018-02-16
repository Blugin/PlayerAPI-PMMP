<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\module;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\entity\Attribute;
use pocketmine\nbt\tag\FloatTag;

class SetSpeedModule extends ModuleCommand{

    public function getDefault() : float{
        return (float) $this->getPlugin()->getConfig()->get('default-speed');
    }

    /**
     * @param float $value
     */
    public function setDefault($value) : void{
        $this->getPlugin()->getConfig()->set('default-speed', $value);
    }

    /**
     * @param String $playerName
     * @param bool   $load = true
     *
     * @return null|float
     */
    public function get(String $playerName, bool $load = true) : ?float{
        $playerData = $this->getPlayerData($playerName, $load);
        if ($playerData !== null) {
            if (!$playerData->hasTag('Speed', FloatTag::class)) {
                $playerData->setFloat('Speed', $this->getDefault());
            }
            return $playerData->getFloat('Speed');
        } else {
            return null;
        }
    }

    public function set(String $playerName, $value) : void{
        $playerData = $this->getPlayerData($playerName);
        if ($playerData !== null) {
            $playerData->setFloat('Speed', $this->getDefault());
            $player = Server::getInstance()->getPlayerExact($playerName);
            if ($player !== null) {
                $this->apply($player);
            }
        }
    }

    /**
     * @param $value
     *
     * @return null|float
     */
    public function validate($value) : ?float{
        if (is_numeric($value)) {
            return (float) $value;
        } else {
            return null;
        }
    }

    public function apply(Player $player){
        safe_var_dump($this->get($player->getLowerCaseName()));
        $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue($this->get($player->getLowerCaseName()));
    }
}