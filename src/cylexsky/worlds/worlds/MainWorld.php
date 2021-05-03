<?php
declare(strict_types=1);

namespace cylexsky\worlds\worlds;

use cylexsky\misc\scoreboards\ScoreboardHandler;
use cylexsky\worlds\BaseWorld;
use pocketmine\entity\Location;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;

class MainWorld extends BaseWorld{

    public function init()
    {
        self::setWorld(Server::getInstance()->getWorldManager()->getWorldByName(self::getName()));
        self::setSpawnPoint(new Location(0.5, 78, 0.5, 0, 180, self::getWorld()));
        self::getWorld()->setTime(World::TIME_NIGHT);
        self::getWorld()->stopTime();
    }

    public static function getName(): string
    {
        return "world";
    }

    public static function teleport(Player $player)
    {
        $player->setGamemode(GameMode::ADVENTURE());
        ScoreboardHandler::sendSpawnScoreboard($player);
        parent::teleport($player); // TODO: Change the autogenerated stub
    }

    public static function forceTeleport(Player $player){
        $player->setGamemode(GameMode::ADVENTURE());
        ScoreboardHandler::sendSpawnScoreboard($player);
        parent::teleport($player); // TODO: Change the autogenerated stub
    }

}