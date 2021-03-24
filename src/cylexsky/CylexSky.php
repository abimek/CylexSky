<?php
declare(strict_types=1);
namespace cylexsky;

use cylexsky\main\ManagerLoader;
use pocketmine\plugin\PluginBase;

class CylexSky extends PluginBase {
    private static $instance;
    protected function onEnable(): void
    {
        self::$instance = $this;
        ManagerLoader::init();
    }
    public static function getInstance(): CylexSky{
        return self::$instance;
    }
}