<?php
declare(strict_types=1);

namespace cylexsky\island;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\managers\Manager;
use core\main\text\TextFormat;
use cylexsky\CylexSky;
use cylexsky\island\commands\IslandCommand;
use cylexsky\island\creation\BaseIsland;
use cylexsky\island\database\IslandDatabaseHandler;
use cylexsky\island\entities\IslandEntityHandler;
use cylexsky\island\listeners\IslandListener;
use cylexsky\island\listeners\JerryListener;
use cylexsky\island\tasks\IslandSaveTask;
use cylexsky\session\database\PlayerSessionDatabaseHandler;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\worlds\worlds\MainWorld;
use InvalidArgumentException;
use pocketmine\Server;
use pocketmine\world\World;

class IslandManager extends Manager{

    public const FIVE_MINUTES = 20 * 60 * 5;

    private static $islands = [];

    private static $topIslands = [];

    public static $islandOwnersToIslandID = [];

    protected function init(): void
    {
        IslandEntityHandler::init();
        IslandDatabaseHandler::init();
        CylexSky::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new IslandSaveTask(), self::FIVE_MINUTES, self::FIVE_MINUTES);
        Server::getInstance()->getPluginManager()->registerEvents(new JerryListener(), CylexSky::getInstance());
        Server::getInstance()->getPluginManager()->registerEvents(new IslandListener(), CylexSky::getInstance());
        Server::getInstance()->getCommandMap()->register("is", new IslandCommand(CylexSky::getInstance(), "is"));
    }

    public static function islandExists(string $id): bool {
        return isset(self::$islands[$id]);
    }

    public static function createIsland(Island $island){
        $id = $island->getId();
        if (!self::islandExists($id)){
            self::$islands[$id] = $island;
            self::$islandOwnersToIslandID[$island->getOwnerName()] = $id;
        }
    }

    public static function queryTopIslands(){
        DatabaseManager::query("SELECT ownerName, wealth FROM islands ORDER BY wealth DESC LIMIT 10", Query::SERVER_DB, [], function ($results){
            self::$topIslands = [];
            if (!is_array($results)){
                return;
            }
            foreach ($results as $result){
                $ownerName = $result["ownerName"];
                $wealth = $result["wealth"];
                self::$topIslands[] = [$ownerName, $wealth];
            }
        });
    }

    public static function getIsland(?string $id): ?Island{
        if($id === null)
            return null;
        if (self::islandExists($id)){
            return self::$islands[$id];
        }
        return null;
    }

    public static function getIslandByOwner(string $name): ?Island{
        if (isset(self::$islandOwnersToIslandID[$name])){
            return self::getIsland(self::$islandOwnersToIslandID[$name]);
        }
        return null;
    }

    public static function saveIslandData(): void {
        foreach (self::$islands as $island){
            if($island->hasBeenChanged()){
                $island->save();
            }
        }
        self::queryTopIslands();
    }

    public static function getTopIslands(): array {
        return self::$topIslands;
    }

    public static function resetIsland(Island $island, BaseIsland $bi){
        $island->reset($bi->getId());
    }

    public static function deleteIsland(string $xuid): void {
        $is = self::getIsland($xuid);
        if ($is === null){
            return;
        }
        foreach ($is->getWorld()->getPlayers() as $player){
            MainWorld::teleport($player);
        }
        self::deleteWorld($is->getWorld());
        unset(self::$islands[$xuid]);
        $owner = $is->getOwnerName();
        if (isset(self::$islandOwnersToIslandID[$owner])){
            unset(self::$islandOwnersToIslandID[$owner]);
        }
        $members = $is->getMembersModule()->getMemberXuids();
        if (Server::getInstance()->getPlayerExact($is->getOwnerName()) !== null){
            $s = SessionManager::getSession($is->getOwner());
            $s->setIsland(null);
            $s->sendNotification(TextFormat::GOLD . $is->getOwnerName() . TextFormat::RED . " deleted their island!");
        }
        $members[] = $is->getOwner();
        DatabaseManager::emptyQuery("UPDATE player_sessions SET island=? WHERE island=?", Query::SERVER_DB, [
         PlayerSessionDatabaseHandler::NULL_STRING,
           $xuid
        ]);
        foreach ($is->getTrustedModule()->getOnlineTrusted() as $player){
            $s = SessionManager::getSession($is->getOwner());
            $s->getTrustedModule()->removeTrustedIsland($is->getId());
            $s->sendNotification(TextFormat::GOLD . $is->getOwnerName() . TextFormat::RED . " deleted their island!");
        }
        foreach ($is->getTrustedModule()->getOfflineTrustedXuids() as $xuid){
            PlayerSessionDatabaseHandler::callableOfflineXuid($xuid, function (PlayerSession $session)use($xuid){
                $session->getTrustedModule()->removeTrustedIsland($xuid);
            });
        }
        DatabaseManager::emptyQuery("DELETE FROM islands WHERE id=?", Query::SERVER_DB, [$xuid]);
    }

    public static function deleteWorld(World $world){
        $path = Server::getInstance()->getDataPath() . "worlds/" . $world->getFolderName();
        Server::getInstance()->getWorldManager()->unloadWorld($world, true);
        self::deleteDir($path);
    }

    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    protected function close(): void
    {
        self::saveIslandData();
    }
}