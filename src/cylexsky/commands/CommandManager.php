<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\managers\Manager;
use pocketmine\Server;

class CommandManager extends Manager {


    protected function init(): void
    {
        Server::getInstance()->getCommandMap()->registerAll("cylexcommand", [
            new SpawnCommand()
        ]);
    }

    protected function close(): void
    {
        // TODO: Implement close() method.
    }
}