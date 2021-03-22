<?php
declare(strict_types=1);
namespace cylexsky;

use core\CylexCore;
use cylexsky\main\ManagerLoader;
use pocketmine\plugin\PluginBase;

class CylexSky extends PluginBase {

    private static $instance;

    public function onEnable()
    {
        self::$instance = $this;
        ManagerLoader::init();
    }

    public static function getInstance(): CylexCore{
        return self::$instance;
    }

}