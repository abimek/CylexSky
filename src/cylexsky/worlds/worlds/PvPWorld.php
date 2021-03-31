<?php
declare(strict_types=1);

namespace cylexsky\worlds\worlds;

use cylexsky\misc\scoreboards\ScoreboardHandler;
use cylexsky\worlds\BaseWorld;
use pocketmine\entity\Location;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class PvPWorld extends BaseWorld{

    public function init()
    {
        self::setWorld(Server::getInstance()->getWorldManager()->getWorldByName(self::getName()));
        self::setSpawnPoint(new Location(0, 0, 0, 0, 0, self::getWorld()));
    }

    public static function getName(): string
    {
        return "pvp";
    }

    public static function teleport(Player $player)
    {
        $player->setGamemode(GameMode::ADVENTURE());
        ScoreboardHandler::sendPvPScoreboard($player);
        parent::teleport($player); // TODO: Change the autogenerated stub
    }

    public static function isPositionInsideArena(Position $position){
        //TODO IMPLEMENT
    }
}