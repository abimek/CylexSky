<?php
declare(strict_types=1);
namespace cylexsky;

use cylexsky\main\ManagerLoader;
use pocketmine\plugin\PluginBase;

class CylexSky extends PluginBase {

    public function onEnable()
    {
        ManagerLoader::init();
    }

}