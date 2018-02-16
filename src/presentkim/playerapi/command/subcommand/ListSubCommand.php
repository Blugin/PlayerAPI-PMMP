<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\subcommand;

use pocketmine\command\CommandSender;
use presentkim\playerapi\command\{
  ExecutableCommand, SubCommand
};

class ListSubCommand extends SubCommand{

    public function __construct(ExecutableCommand $owner){
        parent::__construct('list', $owner);
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        $modules = [];

        if (!empty($args[0]) && is_numeric($args[0])) {
            $pageNumber = (int) $args[0];
            if ($pageNumber <= 0) {
                $pageNumber = 1;
            }
        } else {
            $pageNumber = 1;
        }

        ksort($modules, SORT_NATURAL | SORT_FLAG_CASE);
        $pageHeight = $sender->getScreenLineHeight();
        $commands = array_chunk($modules, $pageHeight);
        $pageNumber = (int) min(count($commands), $pageNumber);
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        $sender->sendMessage($this->translate('header', [
          $pageNumber,
          count($commands),
        ]));
        if (isset($commands[$pageNumber - 1])) {
            foreach ($commands[$pageNumber - 1] as $command) {
                $sender->sendMessage($this->translate('item', []));
            }
        }
        return true;
    }
}