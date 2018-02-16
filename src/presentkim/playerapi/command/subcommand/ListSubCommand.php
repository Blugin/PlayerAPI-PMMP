<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\subcommand;

use pocketmine\command\CommandSender;
use presentkim\playerapi\command\ExecutableCommand;
use presentkim\playerapi\command\module\ModuleCommand;

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
        foreach ($this->getPlugin()->getModules() as $moduleName => $module) {
            if ($module->testPermissionSilent($sender)) {
                $modules[$moduleName] = $module;
            }
        }

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
        /** @var ModuleCommand[][] $modules */
        $modules = array_chunk($modules, $pageHeight);
        $pageNumber = (int) min(count($modules), $pageNumber);
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        $sender->sendMessage($this->translate('header', [
          $pageNumber,
          count($modules),
        ]));
        if (isset($modules[$pageNumber - 1])) {
            foreach ($modules[$pageNumber - 1] as $i => $module) {
                $sender->sendMessage($this->translate('item', [
                  $module->getModuleName(),
                  $module->getUsage($sender),
                ]));
            }
        }
        return true;
    }
}