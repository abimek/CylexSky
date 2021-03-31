<?php
declare(strict_types=1);

namespace cylexsky\island\tasks;

use cylexsky\island\IslandManager;
use pocketmine\scheduler\Task;

class IslandSaveTask extends Task{

    /**
     * Actions to execute when run
     */
    public function onRun(): void
    {
        IslandManager::saveIslandData();
    }
}