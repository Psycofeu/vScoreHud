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
    public function onRun(): void {
        $config = Main::getInstance()->config;
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        $maxPlayers = Server::getInstance()->getMaxPlayers();
        $tickUsage = Server::getInstance()->getTickUsage();
        $tps = Server::getInstance()->getTicksPerSecond();

        foreach ($onlinePlayers as $player) {
            if ($player->isConnected()) {
                $this->sendDisplayObjectivePacket($player, $config->get("title"));

                $linesValue = $config->get('texte');
                foreach ($linesValue as $id => $line) {
                    $line = $this->replaceAll($line, $player, $onlinePlayers, $maxPlayers, $tickUsage, $tps, $config);
                    $this->addLine($id, $line, $player);
                }
            } else {
                $this->getHandler()->cancel();
            }
        }
    }

    private function sendDisplayObjectivePacket(Player $player, string $title): void {
        $packet = SetDisplayObjectivePacket::create(
            SetDisplayObjectivePacket::DISPLAY_SLOT_SIDEBAR,
            $player->getName(),
            $title,
            "dummy",
            SetDisplayObjectivePacket::SORT_ORDER_ASCENDING
        );
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    private function replaceAll(string $line, Player $player, array $onlinePlayers, int $maxPlayers, float $tickUsage, float $tps, $config): string {
        $replacements = [
            "{player_name}" => $player->getName(),
            "{online_player}" => count($onlinePlayers),
            "{max_player}" => $maxPlayers,
            "{item_name}" => $player->getInventory()->getItemInHand()->getName(),
            "{item_count}" => $player->getInventory()->getItemInHand()->getCount(),
            "{x}" => $player->getPosition()->getFloorX(),
            "{y}" => $player->getPosition()->getFloorY(),
            "{z}" => $player->getPosition()->getFloorZ(),
            "{world}" => $player->getWorld()->getFolderName(),
            "{load}" => $tickUsage,
            "{tps}" => $tps,
            "{ip}" => $player->getNetworkSession()->getIp(),
            "{ping}" => $player->getNetworkSession()->getPing(),
            "{health}" => $player->getHealth(),
            "{max_health}" => $player->getMaxHealth(),
            "{food}" => $player->getHungerManager()->getFood(),
            "{max_food}" => $player->getHungerManager()->getMaxFood(),
            "{xp_level}" => $player->getXpManager()->getXpLevel(),
            "{xp_progress}" => $player->getXpManager()->getXpProgress(),
            "{total_xp}" => $player->getXpManager()->getCurrentTotalXp(),
            "{world_player_count}" => count($player->getWorld()->getPlayers())
        ];

        foreach ($replacements as $placeholder => $value) {
            $line = str_replace($placeholder, $value, $line);
        }

        if ($config->get("enable_economy")) {
            $line = $this->replaceEconomy($line, $player, strtolower($config->get("economy_plugin")));
        } else {
            $line = str_replace("{money}", "§ceco off", $line);
        }

        if ($config->get("enable_rank")) {
            $line = $this->replaceRank($line, $player, strtolower($config->get("rank_plugin")));
        } else {
            $line = str_replace("{player_rank}", "§cnot Found", $line);
        }

        if ($config->get("enable_faction") && strtolower($config->get("faction_plugin")) === "piggyfaction") {
            $line = $this->replaceFaction($line, $player);
        } else {
            $line = str_replace("{faction_name}", "§cnot Found", $line);
            $line = str_replace("{faction_power}", "§cnot Found", $line);
            $line = str_replace("{faction_leader}", "§cnot Found", $line);
        }

        return $line;
    }

    private function replaceEconomy(string $line, Player $player, string $economyPlugin): string {
        switch ($economyPlugin) {
            case "economyapi":
                $money = EconomyAPI::getInstance()->myMoney($player) ?? "§ceco down";
                break;
            case "vanillaeconomy":
                $money = vanillaAPI::getInstance()->seeMoney($player) ?? "§ceco down";
                break;
            case "bedrockeconomy":
                $money = BedrockEconomyAPI::getInstance()->getPlayerBalance($player->getName()) ?? "§ceco down";
                break;
            default:
                $money = 0;
                break;
        }
        return str_replace("{money}", $money, $line);
    }
    private function replaceRank(string $line, Player $player, string $rankPlugin): string {
        switch ($rankPlugin) {
            case "pureperm":
                $rank = PurePerms::getInstance()->getUserDataMgr()->getData($player)["group"] ?? "§crank down";
                break;
            case "simplerank":
                $rank = SessionManager::getSessions($player)->getRanks() ?? "§crank down";
                break;
            case "ranksysteme":
                $rank = RankSystem::getInstance()->getSessionManager()->get($player)->getRanks() ?? "§crank down";
                break;
            default:
                $rank = "§cnot Found";
                break;
        }
        return str_replace("{player_rank}", $rank, $line);
    }
    private function replaceFaction(string $line, Player $player): string {
        $faction = PiggyFactions::getInstance()->getPlayerManager()->getPlayerFaction($player->getUniqueId());
        if ($faction !== null) {
            $line = str_replace("{faction_name}", $faction->getName(), $line);
            $line = str_replace("{faction_power}", $faction->getPower(), $line);
            $line = str_replace("{faction_leader}", $faction->getLeader(), $line);
        } else {
            $line = str_replace("{faction_name}", "§cnot Found", $line);
            $line = str_replace("{faction_power}", "§cnot Found", $line);
            $line = str_replace("{faction_leader}", "§cnot Found", $line);
        }
        return $line;
    }
    public function addLine(int $id, string $line, Player $player): void {
        $packet = new ScorePacketEntry();
        $packet->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        $packet->score = $id;
        $packet->scoreboardId = $id;
        $packet->actorUniqueId = $player->getId();
        $packet->objectiveName = $player->getName();
        $packet->customName = $line;
        $this->sendScorePacket($packet, $player);
    }
    private function sendScorePacket(ScorePacketEntry $entry, Player $player): void {
        $packet = new SetScorePacket();
        $packet->entries[] = $entry;
        $packet->type = SetScorePacket::TYPE_CHANGE;
        $player->getNetworkSession()->sendDataPacket($packet);
    }
}
