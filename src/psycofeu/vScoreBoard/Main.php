<?php

namespace psycofeu\vScoreBoard;

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use psycofeu\vScoreBoard\APIS\ScoreTags;

class Main extends PluginBase implements Listener
{
    use SingletonTrait;
    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->saveResource("config.yml");
        $this->saveDefaultConfig();
        $this->getLogger()->notice("vScoreBoard plugin enable | by Psycofeu");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

    }
    public function getConfigFile(): Config
    {
        return new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }
    public function playerJoinEvent(PlayerJoinEvent $event)
    {
        $this->getScheduler()->scheduleRepeatingTask(new task($event->getPlayer()), $this->getConfigFile()->get("refill"));
    }
}