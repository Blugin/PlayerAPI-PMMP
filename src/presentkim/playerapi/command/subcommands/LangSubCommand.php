<?php

declare(strict_types=1);

namespace presentkim\playerapi\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\playerapi\command\{
  PoolCommand, SubCommand
};

class LangSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'lang');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (!empty($args[0]) && ($args[0] = strtolower($args[0]))) {
            $language = $this->plugin->getLanguage();
            $languageList = $language->getLanguageList();
            if (isset($languageList[$args[0]])) {
                $fallbackLangFile = "{$this->plugin->getSourceFolder()}resources/lang/{$args[0]}.ini";
                $dataFolder = $this->plugin->getDataFolder();
                $langFile = "{$dataFolder}lang.ini";
                if (!file_exists($dataFolder)) {
                    mkdir($dataFolder, 0777, true);
                }
                copy($fallbackLangFile, $langFile);
                $language->setLang($language->loadLang($langFile));
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
        return $this->translate('usage', [implode($this->translate('usage.separator', [], false), $this->plugin->getLanguage()->getLanguageList())], false);
    }
}