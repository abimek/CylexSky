<?php
declare(strict_types=1);

namespace cylexsky\island\database;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\data\formatter\JsonFormatter;
use core\main\data\skin_data\SkinImageParser;
use cylexsky\CylexSky;
use cylexsky\island\entities\Henry;
use cylexsky\island\Island;
use cylexsky\island\IslandManager;
use cylexsky\island\modules\LevelModule;
use cylexsky\island\modules\Members;
use cylexsky\island\modules\PermissionModule;
use cylexsky\island\modules\SettingsModule;
use cylexsky\island\modules\TeleportsModule;
use cylexsky\island\modules\TrustedModule;
use cylexsky\island\modules\TutorialModule;
use cylexsky\island\modules\UpgradesModule;
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
            DatabaseManager::emptyQuery("DROP TABLE islands", Query::SERVER_DB);
        }
        DatabaseManager::query("CREATE TABLE IF NOT EXISTS islands(id varchar(36) PRIMARY KEY, world TEXT, owner TEXT, ownerName TEXT, memberData TEXT, tutorialData TEXT, permissionData TEXT, settingsData TEXT, wealth INT, trustedData TEXT, teleportData TEXT, prestigeShards INT, levelData TEXT, upgradeData TEXT, prestigePoints INT)", Query::SERVER_DB, [], function ($results){
            DatabaseManager::query("SELECT * FROM islands", Query::SERVER_DB, [], function ($results){
                foreach ($results as $data){
                    $island = new Island($data["id"], $data["world"], $data["owner"], $data["ownerName"], $data["memberData"], $data["tutorialData"], $data["permissionData"], $data["settingsData"], $data["wealth"], $data["trustedData"], $data["teleportData"], $data["prestigeShards"], $data["levelData"], $data["upgradeData"], $data["prestigePoints"]);
                    IslandManager::createIsland($island);
                    return;
                }
            });
        });
        IslandManager::queryTopIslands();
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
        $upgradeData = self::staticEncodeJson(UpgradesModule::getBaseData());
        if(IslandManager::islandExists($id)){
            return;
        }
        DatabaseManager::query("INSERT IGNORE INTO islands(id, world, owner, ownerName, memberData, tutorialData, permissionData, settingsData, wealth, trustedData, teleportData, prestigeShards, levelData, upgradeData, prestigePoints) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", Query::SERVER_DB, [
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
            $levelData,
            $upgradeData,
            0
        ], function ($results)use($session, $id, $world, $owner, $ownerName, $memberData, $tutorialData, $permissionData, $settingsData, $trustedData, $location, $teleportData, $levelData, $upgradeData){
            $player = $session->getPlayer();
            $rank = $session->getRank();
            $is = new Island($id, $world, $owner, $ownerName, $memberData, $tutorialData, $permissionData, $settingsData, WealthModule::getBaseData(), $trustedData, $teleportData, 0, $levelData, $upgradeData, 0);
            $is->getMembersModule()->setMemberLimit(2+$rank->getLevel());
            $is->setTeleportsIntialLocation($location);
            IslandManager::createIsland($is);
            Server::getInstance()->getWorldManager()->loadWorld($world);
            $is->getWorld()->loadChunk($location->getX() >> 4, $location->getZ() >> 4);
            $entity = new Henry($location, new Skin("henry", SkinImageParser::fromImage(imagecreatefrompng(CylexSky::getInstance()->getDataFolder() . "EntitySkins/henry.png")), "", "geometry.henry", file_get_contents(CylexSky::getInstance()->getDataFolder() . "EntitySkins/geometries/henry.json")));
            $entity->spawnToAll();
            $entity->setIslandId($id);
            $session->setIsland($id);
            $player->teleport($is->getWorld()->getSpawnLocation());
            ScoreboardHandler::sendIslandScoreboard($player);
            $is->getTutorialModule()->join($session);
        });
    }

}