<?php
declare(strict_types=1);

namespace cylexsky\misc\scoreboards;

use core\main\text\TextFormat;
use cylexsky\CylexSky;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;
use pocketmine\Server;

class ScoreboardHandler{

    private static $spawnScoreboard;
    private static $pvpScoreboard;
    private static $islandScoreboard;

    public function __construct()
    {
        Server::getInstance()->getPluginManager()->registerEvents(new Scoreboard(), CylexSky::getInstance());
        self::$spawnScoreboard = new Scoreboard();
        self::$pvpScoreboard = new Scoreboard();
        self::$islandScoreboard = new Scoreboard();
    }

    public static function sendSpawnScoreboard(Player $player){
        $session = SessionManager::getSession($player->getXuid());
        self::$spawnScoreboard->new($player, "", "");
        self::$spawnScoreboard->setLine($player, 0, TextFormat::GRAY . $session->getMoneyModule()->getMoney() . Glyphs::GOLD_COIN);
        self::$spawnScoreboard->setLine($player, 1, TextFormat::AQUA . $session->getLevelModule()->getLevel() . Glyphs::LEVEL_ICON);
        if ($session->getIsland() === null){
            self::$spawnScoreboard->setLine($player, 2, TextFormat::GRAY . "No Island, create one" . Glyphs::ISLAND_ICON);
        }else{
            //TODO AFTER PROPER CREATION
            self::$spawnScoreboard->setLine($player, 2, TextFormat::GRAY . "Not Implemented :clown:" . Glyphs::ISLAND_ICON);
        }
    }

    public static function sendPvPScoreboard(Player $player){
        $session = SessionManager::getSession($player->getXuid());
        self::$pvpScoreboard->new($player, "", "");
        self::$pvpScoreboard->setLine($player, 0, TextFormat::GRAY . $session->getStatsModule()->getKills() . Glyphs::SWORD);
        self::$pvpScoreboard->setLine($player, 1, TextFormat::GRAY . $session->getStatsModule()->getDeaths() . Glyphs::SKULL);
        self::$pvpScoreboard->setLine($player, 2, TextFormat::RED . $session->getStatsModule()->getKD() . TextFormat::GRAY . " Deaths");
    }

    public static function sendIslandScoreboard(Player $player){
        $session = SessionManager::getSession($player->getXuid());
    }
}