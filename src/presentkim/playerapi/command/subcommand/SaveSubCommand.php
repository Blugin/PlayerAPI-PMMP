<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\subcommand;

use pocketmine\command\CommandSender;
use presentkim\playerapi\command\{
  ExecutableCommand, SubCommand
};

class SaveSubCommand extends SubCommand{

    public function __construct(ExecutableCommand $owner){
        parent::__construct('save', $owner);
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        $this->getPlugin()->save();
        $sender->sendMessage($this->translate('success'));
        return true;
    }
}