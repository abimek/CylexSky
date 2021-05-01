<?php
declare(strict_types=1);

namespace cylexsky\session\tasks;

use cylexsky\session\SessionManager;
use pocketmine\scheduler\Task;

class SaveTask extends Task{


    /**
     * Actions to execute when run
     */
    public function onRun(): void
    {
        SessionManager::saveSessions();
    }
}