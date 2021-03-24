<?php
declare(strict_types=1);

namespace cylexsky\island\database;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\data\formatter\JsonFormatter;
use cylexsky\island\Island;
use cylexsky\island\IslandManager;
use cylexsky\island\modules\Members;
use cylexsky\island\modules\TutorialModule;
use cylexsky\session\PlayerSession;

final class IslandDatabaseHandler{
    use JsonFormatter;

    public static function init(){
        DatabaseManager::emptyQuery("CREATE TABLE IF NOT EXISTS islands(id varchar(36) PRIMARY KEY, world TEXT, owner TEXT, ownerName TEXT, memberData TEXT, tutorialData TEXT)");
        $query = new Query("SELECT * FROM islands", [], function ($results){
            foreach ($results as $data){
                $island = new Island($data["id"], $data["world"], $data["owner"], $data["ownerName"], $data["memberData"], $data["tutorialData"]);
                IslandManager::createIsland($island);
                return;
            }
        }
        );
        DatabaseManager::query($query);
    }

    public static function createIsland(PlayerSession $session, string $id, string $world, string $owner, string $ownerName){
        //TODO DECIDE IF I SHOULD CREATE ISLAND CLASS BEFORE OR INSIDE THIS FUNCTION
        $memberData = self::staticEncodeJson(Members::getBaseData());
        $tutorialData = self::staticEncodeJson(TutorialModule::getBaseData());
        if(IslandManager::islandExists($id)){
            return;
        }
        $query = new Query("INSERT IGNORE INTO islands(id, world, owner, ownerName, memberData, tutorialData) VALUES(?, ?, ?, ?, ?, ?)", [
            $id,
            $world,
            $owner,
            $ownerName,
            $memberData,
            $tutorialData
        ], function ($results)use($session, $id, $world, $owner, $ownerName, $memberData, $tutorialData){
            $player = $session->getPlayer();
            $is = new Island($id, $owner, $owner, $ownerName, $memberData, $tutorialData);
            IslandManager::createIsland($is);
            $player->teleport($is->getWorld()->getSpawnLocation());
            $is->getTutorialModule()->join($session);
        });
        DatabaseManager::query($query);
    }

}