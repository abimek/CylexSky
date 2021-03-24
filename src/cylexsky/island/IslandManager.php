<?php
declare(strict_types=1);

namespace cylexsky\island;

use core\main\managers\Manager;
use cylexsky\island\database\IslandDatabaseHandler;

class IslandManager extends Manager{

    private static $islands = [];

    protected function init(): void
    {
        IslandDatabaseHandler::init();
    //    IslandCreationHandler::init();
    }

    public static function islandExists(string $id): bool {
        return isset(self::$islands[$id]);
    }

    public static function createIsland(Island $island){
        $id = $island->getId();
        if (!self::islandExists($id)){
            self::$islands[$id] = $island;
        }
    }

    public static function getIsland(string $id): ?Island{
        if (self::islandExists($id)){
            return self::$islands[$id];
        }
        return null;
    }

    protected function close(): void
    {
        foreach (self::$islands as $island){
            $island->save();
        }
    }
}