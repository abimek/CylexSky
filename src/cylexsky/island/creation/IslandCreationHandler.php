<?php
declare(strict_types=1);

namespace cylexsky\island\creation;

use cylexsky\island\database\IslandDatabaseHandler;
use cylexsky\session\PlayerSession;

class IslandCreationHandler implements IslandTypes {

    public static function init(){
    }

    public static function createIsland(PlayerSession $session, BaseIsland $island){
        if ($session->getIsland() !== null){
            return;
        }
        IslandDatabaseHandler::createIsland($island->getId(), $island->getWorld()->getDisplayName(), $session->getXuid(), $session->getObject()->getUsername(), $session->getObject()->getUsername());
    }
}