<?php

declare(strict_types=1);

namespace presentkim\playerapi\command;

use pocketmine\command\{
  Command, PluginCommand, CommandExecutor, CommandSender
};
use pocketmine\plugin\Plugin;
use presentkim\playerapi\PlayerAPI;
use presentkim\playerapi\lang\PluginLang;

abstract class ExecutableCommand extends PluginCommand implements CommandExecutor{

    /**
     * @var \ReflectionProperty
     */
    protected static $nameReflection;

    /**
     * @var string
     */
    protected $langId;

    /**
     * @param string $name
     * @param Plugin $plugin
     */
    public function __construct(string $name, Plugin $plugin){
        parent::__construct($name, $plugin);
        $this->setExecutor($this);
        if (self::$nameReflection === null) {
            self::$nameReflection = (new \ReflectionClass(Command::class))->getProperty('name');
            self::$nameReflection->setAccessible(true);
        }
        $this->langId = "commands.{$name}";
        $this->setPermission("{$name}.cmd");
        $this->updateTranslation();
    }

    /**
     * @param null|string $id     = null
     * @param string[]    $params = []
     *
     * @return string
     */
    public function translate(?string $id = null, array $params = []) : string{
        return $this->getLanguage()->translate($this->langId . (empty($id) ? '' : ".{$id}"), $params);
    }

    public function updateTranslation() : void{
        self::$nameReflection->setValue($this, $this->translate());
        $this->description = $this->translate('description');
        $this->usageMessage = $this->getUsage();
        $aliases = $this->getLanguage()->getArray("{$this->langId}.aliases");
        if (is_array($aliases)) {
            $this->setAliases($aliases);
        }
    }

    /**
     * @return string
     */
    public function getLangId() : string{
        return $this->langId;
    }

    /**
     * @param string $langId
     */
    public function setLangId(string $langId) : void{
        $this->langId = $langId;
    }

    /**
     * @param CommandSender $sender = null
     *
     * @return string
     */
    public function getUsage(CommandSender $sender = null) : string{
        if (empty($this->usageMessage)) {
            $this->usageMessage = $this->translate('usage');
        }
        return $this->usageMessage;
    }

    /**
     * @return PluginLang
     */
    public function getLanguage() : PluginLang{
        return PlayerAPI::getInstance()->getLanguage();
    }
}