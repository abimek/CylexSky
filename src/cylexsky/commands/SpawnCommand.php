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

class SpawnCommand extends Command{

    public const NAME = "spawn";
    public const DESCRIPTION = "teleports to spawn";
    public const USAGE = TextFormat::RED . "/spawn";

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
        if (!$s->getTeleportModule()->canTeleport()){
            $s->sendNotification("Unable to teleport!");
            return;
        }
        $s->sendGoodNotification("Teleporting to spawn" . TextFormat::GOLD . "...");
        MainWorld::teleport($sender);
        $player = $sender;
        $player->sendMessage(Glyphs::LEXY_LINE_1 . TextFormat::GRAY . "Welcome back to spawn!");
        $player->sendMessage(Glyphs::LEXY_LINE_2 . TextFormat::GRAY . "Today seems like a wonderful business day!");
        $player->sendMessage(Glyphs::LEXY_LINE_3 . TextFormat::GRAY);
    }

}