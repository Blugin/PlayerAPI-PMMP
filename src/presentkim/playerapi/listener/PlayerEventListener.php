<?php

declare(strict_types=1);

namespace presentkim\playerapi\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\{
  PlayerJoinEvent, PlayerRespawnEvent, PlayerMoveEvent
};
use pocketmine\nbt\tag\CompoundTag;
use presentkim\playerapi\PlayerAPI;

class PlayerEventListener implements Listener{

    /** @var PlayerAPI */
    private $plugin = null;

    /**
     * @var bool[string $playerName]
     */
    private $apply = [];

    public function __construct(PlayerAPI $plugin){
        $this->plugin = $plugin;
    }

    /** @param PlayerJoinEvent $event */
    public function onPlayerJoinEvent(PlayerJoinEvent $event) : void{
        $playerName = $event->getPlayer()->getLowerCaseName();
        $playerData = $this->plugin->getPlayerData($playerName, true);
        if ($playerData === null) {
            $this->plugin->setPlayerData($playerName, new CompoundTag($playerName));
        }
        $this->apply[$playerName] = true;
    }

    /** @param PlayerRespawnEvent $event */
    public function onPlayerRespawnEvent(PlayerRespawnEvent $event) : void{
        $this->apply[$event->getPlayer()->getLowerCaseName()] = true;
    }

    /** @param PlayerMoveEvent $event */
    public function onPlayerMoveEvent(PlayerMoveEvent $event) : void{
        $player = $event->getPlayer();
        $playerName = $player->getLowerCaseName();
        if (isset($this->apply[$playerName])) {
            unset($this->apply[$playerName]);
            foreach ($this->plugin->getModules() as $moduleName => $module) {
                $module->apply($player);
            }
        }
    }
}