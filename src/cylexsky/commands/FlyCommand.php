<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\text\TextFormat;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use cylexsky\worlds\worlds\MainWorld;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FlyCommand extends Command{

    public const NAME = "fly";
    public const DESCRIPTION = "fly at your island";
    public const USAGE = TextFormat::RED . "/fly";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, null, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            return;
        }
        $s = SessionManager::getSession($sender->getXuid());
        if (!$s->getMiscModule()->canFlyInRegion()){
            $s->sendNotification("You cant fly here!");
            return;
        }
        if (!$s->getMiscModule()->canFly()){
            $s->sendNotification("Unable to fly");
            return;
        }
        $s->getMiscModule()->toggleFly();
    }

}