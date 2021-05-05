<?php
declare(strict_types=1);

namespace cylexsky\island\entities;

use core\main\data\skin_data\SkinImageParser;
use cylexsky\CylexSky;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class IslandEntityHandler{

    public static function init(){
        EntityFactory::getInstance()->register(Henry::class, function (World $world, CompoundTag $nbt): Henry {
            return new Henry(EntityDataHelper::parseLocation($nbt, $world),  new Skin("henry", SkinImageParser::fromImage(imagecreatefrompng(CylexSky::getInstance()->getDataFolder() . "/EntitySkins/henry.png")), "", "geometry.henry", file_get_contents(CylexSky::getInstance()->getDataFolder() . "EntitySkins/geometries/henry.json")));
        }, ["cylex:henry"], EntityLegacyIds::PLAYER);
    }
}