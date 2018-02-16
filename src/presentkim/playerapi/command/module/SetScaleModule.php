<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\module;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\nbt\tag\FloatTag;

class SetScaleModule extends ModuleCommand{

    public const DEFAULT_KEY = 'default-scale';
    public const TAG_NAME = 'Scale';

    public function getDefault() : float{
        return (float) $this->getPlugin()->getConfig()->get('default-scale');
    }

    /**
     * @param float $value
     */
    public function setDefault($value) : void{
        $this->getPlugin()->getConfig()->set('default-scale', $value);
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
            if (!$playerData->hasTag(static::TAG_NAME, FloatTag::class)) {
                $playerData->setFloat(static::TAG_NAME, $this->getDefault());
            }
            return $playerData->getFloat(static::TAG_NAME);
        } else {
            return null;
        }
    }

    public function set(String $playerName, $value) : void{
        $playerData = $this->getPlayerData($playerName);
        if ($playerData !== null) {
            $playerData->setFloat(static::TAG_NAME, $value);
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
        $player->setScale($this->get($player->getLowerCaseName()));
    }
}