<?php

namespace psycofeu\vScoreBoard;

use _64FF00\PurePerms\PurePerms;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use DaPigGuy\PiggyFactions\PiggyFactions;
use IvanCraft623\RankSystem\RankSystem;
use onebone\economyapi\EconomyAPI;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;
use pocketmine\Server;
use psycofeu\vanillaEconomy\API\vanillaAPI;
use Rank\Session\SessionManager;

class task extends \pocketmine\scheduler\Task
{
    private array $lines = [];
    private Player $player;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function onRun(): void {
        $player = $this->player;
        if ($player->isConnected()){
            $packet = SetDisplayObjectivePacket::create(SetDisplayObjectivePacket::DISPLAY_SLOT_SIDEBAR, $player->getName(), " ", "dummy", SetDisplayObjectivePacket::SORT_ORDER_ASCENDING);
            $player->getNetworkSession()->sendDataPacket($packet);
            $linesValue = Main::getInstance()->getConfigFile()->get('texte');
            foreach ($linesValue as $id => $line) {
                $line = str_replace("{player_name}", $player->getName(), $line);
                $line = str_replace("{online_player}", count(Server::getInstance()->getOnlinePlayers()), $line);
                $line = str_replace("{max_player}", Server::getInstance()->getMaxPlayers(), $line);
                $line = str_replace("{item_name}", $player->getInventory()->getItemInHand()->getName(), $line);
                $line = str_replace("{item_count}", $player->getInventory()->getItemInHand()->getCount(), $line);
                $line = str_replace("{x}", $player->getPosition()->getFloorX(), $line);
                $line = str_replace("{y}", $player->getPosition()->getFloorY(), $line);
                $line = str_replace("{z}", $player->getPosition()->getFloorZ(), $line);
                $line = str_replace("{world}", $player->getWorld()->getFolderName(), $line);
                $line = str_replace("{load}", $player->getServer()->getTickUsage(), $line);
                $line = str_replace("{tps}", $player->getServer()->getTicksPerSecond(), $line);
                $line = str_replace("{ip}", $player->getNetworkSession()->getIp(), $line);
                $line = str_replace("{ping}", $player->getNetworkSession()->getPing(), $line);
                $line = str_replace("{health}", $player->getHealth(), $line);
                $line = str_replace("{max_health}", $player->getMaxHealth(), $line);
                $line = str_replace("{food}", $player->getHungerManager()->getFood(), $line);
                $line = str_replace("{max_food}", $player->getHungerManager()->getMaxFood(), $line);
                $line = str_replace("{xp_level}", $player->getXpManager()->getXpLevel(), $line);
                $line = str_replace("{xp_progress}", $player->getXpManager()->getXpProgress(), $line);
                $line = str_replace("{total_xp}", $player->getXpManager()->getCurrentTotalXp(), $line);
                $line = str_replace("{world_player_count}", count($player->getWorld()->getPlayers()), $line);
                $config = Main::getInstance()->getConfigFile();
                if ($config->get("enable_economy")) {
                    switch (strtolower($config->get("economy_plugin"))) {
                        case "economyapi":
                            $line = str_replace("{money}", EconomyAPI::getInstance()->myMoney($player) ?? "§ceco down", $line);
                            break;
                        case "vanillaeconomy":
                            $line = str_replace("{money}", vanillaAPI::getInstance()->seeMoney($player) ?? "§ceco down", $line);
                            break;
                        case "bedrockeconomy":
                            $line = str_replace("{money}", BedrockEconomyAPI::getInstance()->getPlayerBalance($player->getName()) ?? "§ceco down", $line);
                            break;
                        default:
                            $line = str_replace("{money}", 0, $line);
                            break;
                    }
                } else {
                    $line = str_replace("{money}", "§ceco off", $line);
                }

                if ($config->get("enable_rank")) {
                    switch (strtolower($config->get("rank_plugin"))) {
                        case "pureperm":
                            $line = str_replace("{player_rank}", PurePerms::getInstance()->getUserDataMgr()->getData($player)["group"] ?? "§crank down", $line);
                            break;
                        case "simplerank":
                            $line = str_replace("{player_rank}", SessionManager::getSessions($player)->getRanks() ?? "§crank down", $line);
                            break;
                        case "ranksysteme":
                            $line = str_replace("{player_rank}", RankSystem::getInstance()->getSessionManager()->get($player)->getRanks() ?? "§crank down", $line);
                            break;
                        default:
                            $line = str_replace("{player_rank}", "§cnot Found", $line);
                            break;
                    }
                } else {
                    $line = str_replace("{player_rank}", "§cnot Found", $line);
                }

                if ($config->get("enable_faction") && strtolower($config->get("faction_plugin")) === "piggyfaction") {
                    $line = str_replace("{faction_name}", PiggyFactions::getInstance()->getPlayerManager()->getPlayerFaction($player->getUniqueId())->getName(), $line);
                    $line = str_replace("{faction_power}", PiggyFactions::getInstance()->getPlayerManager()->getPlayerFaction($player->getUniqueId())->getPower(), $line);
                    $line = str_replace("{faction_leader}", PiggyFactions::getInstance()->getPlayerManager()->getPlayerFaction($player->getUniqueId())->getLeader(), $line);
                } else {
                    $line = str_replace("{faction_name}", "§cnot Found", $line);
                    $line = str_replace("{faction_power}", "§cnot Found", $line);
                    $line = str_replace("{faction_leader}", "§cnot Found", $line);
                }
                $this->addLine($id, $line);
            }

        }else $this->getHandler()->cancel();
    }

    public function addLine(int $id, string $line): void {
        $player = $this->player;
        $packet = new ScorePacketEntry();
        $packet->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        if(isset($this->lines[$id])){
            $pk = new SetScorePacket();
            $pk->entries[] = $this->lines[$id];
            $pk->type = SetScorePacket::TYPE_REMOVE;
            $player->getNetworkSession()->sendDataPacket($pk);
            unset($this->lines[$id]);
        }
        $packet->score = $id;
        $packet->scoreboardId = $id;
        $packet->actorUniqueId = $player->getId();
        $packet->objectiveName = $this->player->getName();
        $packet->customName = $line;
        $this->lines[$id] = $packet;
        $pkt = new SetScorePacket();
        $pkt->entries[] = $packet;
        $pkt->type = SetScorePacket::TYPE_CHANGE;
        $player->getNetworkSession()->sendDataPacket($pkt);
    }

}