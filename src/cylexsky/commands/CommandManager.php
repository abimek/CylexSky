<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\managers\Manager;
use pocketmine\Server;

class CommandManager extends Manager {


    protected function init(): void
    {
        Server::getInstance()->getCommandMap()->registerAll("cylexcommand", [
            new SpawnCommand(),
            new FlyCommand(),
            new AdminCommand(),
            new TpaCommand(),
            new TpaAcceptCommand(),
            new TogglesCommand(),
            new TopMoneyCommand(),
            new PayCommand(),
            new ItemInfoCommand(),
            new EmojisCommand()
        ]);
    }

    protected function close(): void
    {
    }
}