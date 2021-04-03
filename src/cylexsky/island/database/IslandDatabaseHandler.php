<?php
declare(strict_types=1);

namespace cylexsky\island\database;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\data\formatter\JsonFormatter;
use core\main\data\skin_data\SkinImageParser;
use cylexsky\CylexSky;
use cylexsky\island\entities\Jerry;
use cylexsky\island\Island;
use cylexsky\island\IslandManager;
use cylexsky\island\modules\LevelModule;
use cylexsky\island\modules\Members;
use cylexsky\island\modules\PermissionModule;
use cylexsky\island\modules\SettingsModule;
use cylexsky\island\modules\TeleportsModule;
use cylexsky\island\modules\TrustedModule;
use cylexsky\island\modules\TutorialModule;
use cylexsky\island\modules\WealthModule;
use cylexsky\misc\scoreboards\ScoreboardHandler;
use cylexsky\session\PlayerSession;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\Server;

final class IslandDatabaseHandler{
    use JsonFormatter;

    public static function init(){
        if (CylexSky::getInstance()->shouldReset()){
            DatabaseManager::emptyQuery("DROP TABLE islands");
        }
        DatabaseManager::emptyQuery("CREATE TABLE IF NOT EXISTS islands(id varchar(36) PRIMARY KEY, world TEXT, owner TEXT, ownerName TEXT, memberData TEXT, tutorialData TEXT, permissionData TEXT, settingsData TEXT, wealth INT, trustedData TEXT, teleportData TEXT, prestigeShards INT, levelData TEXT)");
        $query = new Query("SELECT * FROM islands", [], function ($results){
            foreach ($results as $data){
                $island = new Island($data["id"], $data["world"], $data["owner"], $data["ownerName"], $data["memberData"], $data["tutorialData"], $data["permissionData"], $data["settingsData"], $data["wealth"], $data["trustedData"], $data["teleportData"], $data["prestigeShards"], $data["levelData"]);
                IslandManager::createIsland($island);
                return;
            }
        }
        );
        DatabaseManager::query($query);
    }


    public static function createIsland(PlayerSession $session, string $id, string $world, string $owner, string $ownerName, Location $location){
        //TODO DECIDE IF I SHOULD CREATE ISLAND CLASS BEFORE OR INSIDE THIS FUNCTION
        $memberData = self::staticEncodeJson(Members::getBaseData());
        $tutorialData = self::staticEncodeJson(TutorialModule::getBaseData());
        $permissionData = self::staticEncodeJson(PermissionModule::getBaseData());
        $settingsData = self::staticEncodeJson(SettingsModule::getBaseData());
        $trustedData = self::staticEncodeJson(TrustedModule::getBaseData());
        $teleportData = self::staticEncodeJson(TeleportsModule::getBaseData());
        $levelData = self::staticEncodeJson(LevelModule::getBaseData());
        if(IslandManager::islandExists($id)){
            return;
        }
        $query = new Query("INSERT IGNORE INTO islands(id, world, owner, ownerName, memberData, tutorialData, permissionData, settingsData, wealth, trustedData, teleportData, prestigeShards, levelData) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $id,
            $world,
            $owner,
            $ownerName,
            $memberData,
            $tutorialData,
            $permissionData,
            $settingsData,
            WealthModule::getBaseData(),
            $trustedData,
            $teleportData,
            0,
            $levelData
        ], function ($results)use($session, $id, $world, $owner, $ownerName, $memberData, $tutorialData, $permissionData, $settingsData, $trustedData, $location, $teleportData, $levelData){
            $player = $session->getPlayer();
            $is = new Island($id, $world, $owner, $ownerName, $memberData, $tutorialData, $permissionData, $settingsData, WealthModule::getBaseData(), $trustedData, $teleportData, 0, $levelData);
            $is->setTeleportsIntialLocation($location);
            IslandManager::createIsland($is);
            Server::getInstance()->getWorldManager()->loadWorld($world);
            $is->getWorld()->loadChunk($location->getX() >> 4, $location->getZ() >> 4);
            $entity = new Jerry($location, new Skin("jerry", SkinImageParser::fromImage(imagecreatefrompng(CylexSky::getInstance()->getDataFolder() . "EntitySkins/jerry.png"))));
            $entity->spawnToAll();
            $entity->setIslandId($id);
            $session->setIsland($id);
            $player->teleport($is->getWorld()->getSpawnLocation());
            ScoreboardHandler::sendIslandScoreboard($player);
            $is->getTutorialModule()->join($session);
        });
        DatabaseManager::query($query);
    }

}