<?php

declare(strict_types=1);

namespace presentkim\playerapi\command;

use pocketmine\command\{
  Command, CommandSender
};
use pocketmine\plugin\Plugin;

class PoolCommand extends ExecutableCommand{

    /**
     * @var SubCommand[]
     */
    protected $subCommands = [];

    /**
     * @param Plugin       $plugin
     * @param string       $name
     * @param SubCommand[] $subCommands = []
     */
    public function __construct(string $name, Plugin $plugin, array $subCommands = []){
        $this->subCommands = $subCommands;
        parent::__construct($name, $plugin);
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
        if (!empty($args[0])) {
            $label = array_shift($args);
            foreach ($this->subCommands as $key => $value) {
                if ($value->checkLabel($label)) {
                    $value->execute($sender, $args);
                    return true;
                }
            }
        }
        $sender->sendMessage($this->getLanguage()->translate('commands.generic.usage', [$this->getUsage($sender)]));
        return true;
    }

    /**
     * @param bool $updateSubCommand = true;
     */
    public function updateTranslation(bool $updateSubCommand = true) : void{
        parent::updateTranslation();
        if ($updateSubCommand) {
            foreach ($this->subCommands as $key => $subCommand) {
                $subCommand->updateTranslation();
            }
        }
    }

    /** @return SubCommand[] */
    public function getSubCommands() : array{
        return $this->subCommands;
    }

    /** @param SubCommand[] $subCommands */
    public function setSubCommands(array $subCommands) : void{
        $this->subCommands = $subCommands;
    }

    /** @param SubCommand $subCommand */
    public function addSubCommand(SubCommand $subCommand) : void{
        $this->subCommands[] = $subCommand;
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