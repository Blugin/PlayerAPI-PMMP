<?php

declare(strict_types=1);

namespace presentkim\playerapi;

use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use presentkim\playerapi\command\PoolCommand;
use presentkim\playerapi\command\module\{
  ModuleCommand, SetScaleModule, SetSpeedModule
};
use presentkim\playerapi\command\subcommand\{
  ListSubCommand, LangSubCommand, ReloadSubCommand, SaveSubCommand
};
use presentkim\playerapi\lang\PluginLang;
use presentkim\playerapi\listener\PlayerEventListener;

class PlayerAPI extends PluginBase{

    /**
     * @var PlayerAPI
     */
    private static $instance;

    /**
     * @return PlayerAPI
     */
    public static function getInstance() : PlayerAPI{
        return self::$instance;
    }

    /**
     * @var PluginLang
     */
    private $language;

    /**
     * @var PoolCommand
     */
    private $command;

    /**
     * @var ModuleCommand[]
     */
    private $modules = [];

    /**
     * @var CompoundTag[]
     */
    private $playerDatas = [];

    public function onLoad() : void{
        if (self::$instance === null) {
            self::$instance = $this;
        }
    }

    public function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener($this), $this);
        $this->load();
    }

    public function onDisable() : void{
        $this->save();
    }

    /**
     * @return PluginLang
     */
    public function getLanguage() : PluginLang{
        return $this->language;
    }

    /**
     * @return string
     */
    public function getSourceFolder() : string{
        $pharPath = \Phar::running();
        if (empty($pharPath)) {
            return dirname(__FILE__, 4) . DIRECTORY_SEPARATOR;
        } else {
            return $pharPath . DIRECTORY_SEPARATOR;
        }
    }

    public function load() : void{
        $this->playerDatas = [];
        $playerDataFolder = "{$this->getDataFolder()}players/";
        if (!file_exists($playerDataFolder)) {
            mkdir($playerDataFolder, 0777, true);
        }
        $this->language = new PluginLang($this);
        $this->reloadConfig();
        if ($this->command == null) {
            $this->command = new PoolCommand('playerapi', $this);
            $this->command->addSubCommand(new ListSubCommand($this->command));
            $this->command->addSubCommand(new LangSubCommand($this->command));
            $this->command->addSubCommand(new ReloadSubCommand($this->command));
            $this->command->addSubCommand(new SaveSubCommand($this->command));

            $this->addModule(new SetSpeedModule('setspeed', $this));
            $this->addModule(new SetScaleModule('setscale', $this));
        }
        $commandMap = $this->getServer()->getCommandMap();
        $fallbackPrefix = strtolower($this->getName());
        $this->command->updateTranslation(true);
        if ($this->command->isRegistered()) {
            $commandMap->unregister($this->command);
        }
        $commandMap->register($fallbackPrefix, $this->command);
        foreach ($this->modules as $moduleName => $module) {
            $module->updateTranslation();
            if ($module->isRegistered()) {
                $commandMap->unregister($module);
            }
            $commandMap->register($fallbackPrefix, $module);
        }
    }

    public function save() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }
        $this->saveConfig();
        $playerDataFolder = "{$dataFolder}players/";
        if (!file_exists($playerDataFolder)) {
            mkdir($playerDataFolder, 0777, true);
        }
        $nbtStream = new BigEndianNBTStream();
        foreach ($this->playerDatas as $playerName => $compoundTag) {
            $nbtStream->setData($compoundTag);
            file_put_contents("{$playerDataFolder}{$playerName}.dat", $nbtStream->writeCompressed());
        }
    }

    /**
     * @param string $name
     *
     * @return PoolCommand
     */
    public function getCommand(string $name = '') : PoolCommand{
        return $this->command;
    }

    /**
     * @return ModuleCommand[]
     */
    public function getModules() : array{
        return $this->modules;
    }

    /**
     * @param ModuleCommand[] $modules
     */
    public function setModules(array $modules) : void{
        $this->modules = $modules;
    }

    /**
     * @param string $moduleName
     *
     * @return null|ModuleCommand
     */
    public function getModule(string $moduleName) : ?ModuleCommand{
        return $this->modules[$moduleName] ?? null;
    }

    /**
     * @param ModuleCommand $module
     */
    public function addModule(ModuleCommand $module) : void{
        $this->modules[$module->getModuleName()] = $module;
    }

    /**
     * @return CompoundTag[]
     */
    public function getPlayerDatas() : array{
        return $this->playerDatas;
    }

    /**
     * @param string $playerName
     *
     * @param bool   $load = false
     *
     * @return null|CompoundTag
     */
    public function getPlayerData(string $playerName, bool $load = false) : ?CompoundTag{
        $playerName = strtolower($playerName);
        if (isset($this->playerDatas[$playerName])) {
            return $this->playerDatas[$playerName];
        } elseif ($load) {
            return $this->loadPlayerData($playerName);
        }
        return null;
    }

    /**
     * @param string      $playerName
     * @param CompoundTag $playerData
     */
    public function setPlayerData(string $playerName, CompoundTag $playerData) : void{
        $this->playerDatas[strtolower($playerName)] = $playerData;
    }

    /**
     * @param string $playerName
     *
     * @return null|CompoundTag
     */
    public function loadPlayerData(string $playerName) : ?CompoundTag{
        $playerName = strtolower($playerName);
        $file = "{$this->getDataFolder()}players/{$playerName}.dat";
        if (file_exists($file)) {
            try{
                $nbtStream = new BigEndianNBTStream();
                $nbtStream->readCompressed(file_get_contents($file));
                $this->playerDatas[$playerName] = $nbtStream->getData();
                return $this->playerDatas[$playerName];
            } catch (\Throwable $e){
                $this->getLogger()->critical($e->getMessage());
            }
        }
        return null;
    }
}
