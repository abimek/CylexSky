<?php
declare(strict_types=1);

namespace cylexsky\misc\scoreboards;

use cylexsky\session\SessionManager;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ScoreboardTask extends Task{

    /**
     * Actions to execute when run
     */
    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $session = SessionManager::getSession($player->getXuid());
            if ($session->getTogglesModule()->scoreboards()){
                $levelName = $player->getWorld()->getFolderName();
                switch ($levelName){
                    case "world":
                        ScoreboardHandler::sendSpawnScoreboard($player);
                        return;
                    case "pvp":
                        ScoreboardHandler::sendPvPScoreboard($player);
                        return;
                    default:
                        ScoreboardHandler::sendIslandScoreboard($player);
                        return;
                }
            }
        }
    }
}