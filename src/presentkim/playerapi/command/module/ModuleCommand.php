<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\module;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\{
  Command, CommandSender
};
use pocketmine\nbt\tag\CompoundTag;
use presentkim\playerapi\PlayerAPI;
use presentkim\playerapi\command\ExecutableCommand;

abstract class ModuleCommand extends ExecutableCommand{

    public const DEFAULT_KEY = '';
    public const TAG_NAME = '';

    /**
     * @param CommandSender $sender
     * @param Command       $command
     * @param string        $label
     * @param string[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if (!empty($args[0]) && !empty($args[1])) {
            $value = $args[1] === '-' ? $this->getDefault() : $this->validate($args[1]);
            if ($value === null) {
                $sender->sendMessage($this->getLanguage()->translate('commands.generic.invalid', [$args[1]]));
            } elseif ($args[0] === '-') {
                $this->setDefault($value);
                $sender->sendMessage($this->translate('setDefault', [$value]));
            } else {
                $player = Server::getInstance()->getPlayer($args[0]);
                if ($player !== null) {
                    $playerName = $player->getName();
                } else {
                    $playerName = strtolower($args[0]);
                }
                $playerData = $this->getPlayerData($playerName);
                if ($playerData === null) {
                    $sender->sendMessage($this->getLanguage()->translate('commands.generic.player.notFound', [$args[0]]));
                } else {
                    $this->set($playerName, $value);
                    $sender->sendMessage($this->translate('set', [
                      $playerName,
                      $value,
                    ]));
                }
            }
            return true;
        }
        return false;
    }

    public function getModuleName() : string{
        return $this->translate('name');
    }

    /**
     * @param string $playerName
     *
     * @param bool   $load = false
     *
     * @return null|CompoundTag
     */
    public function getPlayerData(String $playerName, bool $load = true) : ?CompoundTag{
        /** @var PlayerAPI $plugin */
        $plugin = $this->getPlugin();
        return $plugin->getPlayerData($playerName, $load);
    }

    abstract public function getDefault();

    abstract public function get(String $playerName, bool $load = true);

    abstract public function setDefault($value) : void;

    abstract public function set(String $playerName, $value) : void;

    abstract public function validate($value);

    abstract public function apply(Player $player);
}