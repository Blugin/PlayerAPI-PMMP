<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\subcommand;

use pocketmine\command\CommandSender;
use presentkim\playerapi\command\{
  ExecutableCommand, SubCommand
};

class LangSubCommand extends SubCommand{

    public function __construct(ExecutableCommand $owner){
        parent::__construct('lang', $owner);
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (!empty($args[0]) && ($args[0] = strtolower($args[0]))) {
            $language = $this->getLanguage();
            $languageList = $language->getLanguageList();
            if (isset($languageList[$args[0]])) {
                $plugin = $this->getPlugin();
                $dataFolder = $plugin->getDataFolder();
                if (!file_exists($dataFolder)) {
                    mkdir($dataFolder, 0777, true);
                }
                $langResourceFile = "{$plugin->getSourceFolder()}resources/lang/{$args[0]}.ini";
                $langDataFile = "{$dataFolder}lang.ini";
                copy($langResourceFile, $langDataFile);
                $language->setLang($language->loadLang($langDataFile));
                $sender->sendMessage($this->translate('success', [
                  $args[0],
                  $languageList[$args[0]],
                ]));
                return true;
            } else {
                $sender->sendMessage($this->translate('notFound', [$args[0]]));
            }
        }
        return false;
    }

    /**
     * @param CommandSender $sender
     *
     * @return string
     */
    public function getUsage(CommandSender $sender = null) : string{
        return $this->translate('usage', [implode($this->translate('usage.separator'), $this->getPlugin()->getLanguage()->getLanguageList())]);
    }
}