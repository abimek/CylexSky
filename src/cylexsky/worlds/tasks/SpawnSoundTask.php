<?php
declare(strict_types=1);

namespace cylexsky\worlds\tasks;

use cylexsky\session\SessionManager;
use cylexsky\utils\Sounds;
use cylexsky\worlds\worlds\MainWorld;
use pocketmine\scheduler\Task;

class SpawnSoundTask extends Task{

    /**
     * Actions to execute when run
     */
    public function onRun(): void
    {
        foreach (MainWorld::getWorld()->getPlayers() as $player){
            $session = SessionManager::getSession($player->getXuid());
            if ($session === null){
                continue;
            }
            if ($session->getTogglesModule()->spawnSounds()){
                Sounds::sendSoundPlayer($player, Sounds::BIRD_CHIRPING_SOUND, 1.0, 3.0);
            }
        }
    }
}