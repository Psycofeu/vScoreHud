<?php

namespace psycofeu\vScoreBoard;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase
{
    use SingletonTrait;

    public Config $config;
    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->getLogger()->notice("vScoreBoard plugin enable | by Psycofeu");
        $this->config = $this->getConfig();
        $this->getScheduler()->scheduleRepeatingTask(new task(), $this->config->get("refill")*20);
    }
}