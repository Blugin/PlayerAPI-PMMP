<?php

declare(strict_types=1);

namespace presentkim\playerapi\command;

use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use presentkim\playerapi\PlayerAPI;
use presentkim\playerapi\util\Utils;

abstract class SubCommand{

    /**
     * @var PoolCommand
     */
    protected $owner;

    /**
     * @var string
     */
    protected $langId;

    /**
     * @var string
     */
    protected $permission;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string[]
     */
    protected $aliases;

    /**
     * @var string
     */
    protected $usage;

    /**
     * @param string      $label
     * @param PoolCommand $owner
     */
    public function __construct(string $label, PoolCommand $owner){
        $this->owner = $owner;
        $this->langId = "{$owner->getLangId()}.{$label}";
        $this->permission = "{$owner->getPermission()}.{$label}";
        $this->updateTranslation();
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     */
    public function execute(CommandSender $sender, array $args) : void{
        if (!$this->checkPermission($sender)) {
            $sender->sendMessage($this->getOwner()->getLanguage()->translate('commands.generic.permission'));
        } elseif (!$this->onCommand($sender, $args)) {
            $sender->sendMessage($this->getOwner()->getLanguage()->translate('commands.generic.usage', [$this->getUsage($sender)]));
        }
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    abstract public function onCommand(CommandSender $sender, array $args) : bool;

    /**
     * @param null|string $id     = null
     * @param string[]    $params = []
     *
     * @return string
     */
    public function translate(?string $id = null, array $params = []) : string{
        return $this->getOwner()->getLanguage()->translate($this->langId . (empty($id) ? '' : ".{$id}"), $params);
    }

    public function updateTranslation() : void{
        $this->label = $this->translate();
        $aliases = $this->getOwner()->getLanguage()->getArray("{$this->langId}.aliases");
        if (is_array($aliases)) {
            $this->aliases = $aliases;
        }
        $this->usage = $this->translate('usage');
    }

    /**
     * @param string $label
     *
     * @return bool
     */
    public function checkLabel(string $label) : bool{
        return strcasecmp($label, $this->label) === 0 || $this->aliases && Utils::in_arrayi($label, $this->aliases);
    }

    /**
     * @param CommandSender $target
     *
     * @return bool
     */
    public function checkPermission(CommandSender $target) : bool{
        if ($this->permission === null) {
            return true;
        } else {
            return $target->hasPermission($this->permission);
        }
    }

    /**
     * @return PoolCommand
     */
    public function getOwner() : PoolCommand{
        return $this->owner;
    }

    /**
     * @return PlayerAPI
     */
    public function getPlugin() : Plugin{
        return $this->owner->getPlugin();
    }

    /**
     * @return string
     */
    public function getLangId() : string{
        return $this->langId;
    }

    /**
     * @return string
     */
    public function getPermission() : string{
        return $this->permission;
    }

    /**
     * @return string
     */
    public function getLabel() : string{
        return $this->label;
    }

    /**
     * @return string[]
     */
    public function getAliases() : array{
        return $this->aliases;
    }

    /**
     * @param CommandSender $sender = null
     *
     * @return string
     */
    public function getUsage(CommandSender $sender = null) : string{
        return $this->usage;
    }

    /**
     * @param string
     */
    public function setLangId(string $langId) : void{
        $this->langId = $langId;
    }

    /**
     * @param string
     */
    public function setPermission(string $permission) : void{
        $this->permission = $permission;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label) : void{
        $this->label = $label;
    }

    /**
     * @param string[] $aliases
     */
    public function setAliases(array $aliases) : void{
        $this->aliases = $aliases;
    }

    /**
     * @param string $usage
     */
    public function setUsage(string $usage) : void{
        $this->usage = $usage;
    }
}