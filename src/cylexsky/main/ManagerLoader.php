<?php
declare(strict_types=1);

namespace cylexsky\main;

use cylexsky\misc\MiscManager;
use Exception;

final class ManagerLoader{

    public const MANAGERS = [
        MiscManager::class
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