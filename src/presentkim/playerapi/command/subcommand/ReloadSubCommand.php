<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\subcommand;

use pocketmine\command\CommandSender;
use presentkim\playerapi\command\ExecutableCommand;

class ReloadSubCommand extends SubCommand{

    public function __construct(ExecutableCommand $owner){
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