<?php
declare(strict_types=1);

namespace cylexsky\island\creation;

use cylexsky\island\creation\types\NormalIsland;
use cylexsky\island\database\IslandDatabaseHandler;
use cylexsky\session\PlayerSession;
use cylexsky\utils\RankIds;

class IslandCreationHandler implements IslandTypes, RankIds {

    public const TYPES = [
        self::NORMAL_ISLAND => ["Normal", self::ROOKIE, NormalIsland::class],
    ];

    public static function init(){
    }

    public static function createIsland(PlayerSession $session, BaseIsland $island){
        if ($session->getIsland() !== null){
            return;
        }
        IslandDatabaseHandler::createIsland($session, $island->getId(), $island->getId(), $session->getXuid(), $session->getObject()->getUsername(), $island->getJamesLocation());
}

    public static function getTypes(): array {
        return self::TYPES;
    }
}