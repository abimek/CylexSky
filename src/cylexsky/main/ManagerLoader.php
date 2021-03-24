<?php
declare(strict_types=1);

namespace cylexsky\main;

use cylexsky\island\IslandManager;
use cylexsky\misc\MiscManager;
use cylexsky\session\SessionManager;
use cylexsky\worlds\WorldManager;
use Exception;

final class ManagerLoader{

    public const MANAGERS = [
        WorldManager::class,
        MiscManager::class,
        SessionManager::class,
        IslandManager::class
    ];

    private static $managers = [];

    public static function init(){
        self::$managers = array_map(function($manager){
            try{
                return new $manager;
            }catch (Exception $exception){
                var_dump("Exception while loading managers: " . $exception->getMessage());
                return null;
            }
            }, self::MANAGERS);
    }
}