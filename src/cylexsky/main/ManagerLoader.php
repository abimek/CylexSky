<?php
declare(strict_types=1);

namespace cylexsky\main;

use core\main\managers\Manager;
use cylexsky\commands\CommandManager;
use cylexsky\island\IslandManager;
use cylexsky\misc\MiscManager;
use cylexsky\session\SessionManager;
use cylexsky\worlds\WorldManager;
use Exception;

final class ManagerLoader{

    public const MANAGERS = [
        CommandManager::class,
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

    public static function close(){
        foreach (self::$managers as $manager){
            if ($manager instanceof Manager){
                $manager->disable();
            }
        }
    }
}