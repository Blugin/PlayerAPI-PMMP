<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\playerapi\command\{
  PoolCommand, SubCommand
};

class ReloadSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct('reload', $owner);
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        $this->getPlugin()->load();
        $sender->sendMessage($this->translate('success'));
        return true;
    }
}