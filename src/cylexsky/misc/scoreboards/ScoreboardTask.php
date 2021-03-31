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
            if ($session !== null && $session->getTogglesModule()->scoreboards()){
                ScoreboardHandler::sendAppropriateScoreboard($session);
            }
        }
    }
}