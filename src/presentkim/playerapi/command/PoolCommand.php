<?php

declare(strict_types=1);

namespace presentkim\playerapi\command;

use pocketmine\command\{
  Command, PluginCommand, CommandExecutor, CommandSender
};
use presentkim\playerapi\PlayerAPI as Plugin;

class PoolCommand extends PluginCommand implements CommandExecutor{

    /**
     * @var string
     */
    protected $langId;

    /**
     * @var SubCommand[]
     */
    protected $subCommands = [];

    /**
     * @var \ReflectionProperty
     */
    private $nameReflection = null;

    /**
     * @param Plugin       $owner
     * @param string       $name
     * @param SubCommand[] $subCommands
     */
    public function __construct(Plugin $owner, string $name, SubCommand ...$subCommands){
        parent::__construct($name, $owner);
        $this->setExecutor($this);
        $this->subCommands = $subCommands;
        $this->nameReflection = (new \ReflectionClass(Command::class))->getProperty('name');
        $this->nameReflection->setAccessible(true);
        $this->langId = "commands.{$name}";
        $this->setPermission("{$name}.cmd");
        $this->updateTranslation();
    }

    /**
     * @param CommandSender $sender
     * @param Command       $command
     * @param string        $label
     * @param string[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if (isset($args[0])) {
            $label = array_shift($args);
            foreach ($this->subCommands as $key => $value) {
                if ($value->checkLabel($label)) {
                    $value->execute($sender, $args);
                    return true;
                }
            }
        }
        /** @var Plugin $plugin */
        $plugin = $this->getPlugin();
        $sender->sendMessage($plugin->getLanguage()->translate('commands.generic.usage', [$this->getUsage($sender)]));
        return true;
    }

    /**
     * @param null|string $id     = null
     * @param string[]    $params = []
     *
     * @return string
     */
    public function translate(?string $id = null, array $params = []) : string{
        /** @var Plugin $plugin */
        $plugin = $this->getPlugin();
        return $plugin->getLanguage()->translate($this->langId . (empty($id) ? '' : ".{$id}"), $params);
    }

    /**
     * @param bool $updateSubCommand = true;
     */
    public function updateTranslation(bool $updateSubCommand = true) : void{
        $this->nameReflection->setValue($this, $this->translate());
        $this->description = $this->translate('description');
        $this->usageMessage = $this->getUsage();
        /** @var Plugin $plugin */
        $plugin = $this->getPlugin();
        $aliases = $plugin->getLanguage()->getArray("{$this->langId}.aliases");
        if (is_array($aliases)) {
            $this->setAliases($aliases);
        }
        if ($updateSubCommand) {
            foreach ($this->subCommands as $key => $value) {
                $value->updateTranslation();
            }
        }
    }

    /**
     * @return string
     */
    public function getLangId() : string{
        return $this->langId;
    }

    /** @return SubCommand[] */
    public function getSubCommands() : array{
        return $this->subCommands;
    }

    /**
     * @param string $langId
     */
    public function setLangId(string $langId) : void{
        $this->langId = $langId;
    }

    /** @param SubCommand[] $subCommands */
    public function setSubCommands(array $subCommands) : void{
        $this->subCommands = $subCommands;
    }

    /** @param SubCommand::class $subCommandClass */
    public function createSubCommand($subCommandClass) : void{
        $this->subCommands[] = new $subCommandClass($this);
    }

    /**
     * @param null|CommandSender $sender
     *
     * @return string
     */
    public function getUsage(CommandSender $sender = null) : string{
        $subCommands = [];
        foreach ($this->subCommands as $key => $subCommand) {
            if ($sender === null || $subCommand->checkPermission($sender)) {
                $subCommands[] = $subCommand->getLabel();
            }
        }
        return $this->translate('usage', [implode($this->translate('usage.separator'), $subCommands)]);
    }
}