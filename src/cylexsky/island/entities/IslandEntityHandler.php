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
        EntityFactory::getInstance()->register(Jerry::class, function (World $world, CompoundTag $nbt): Jerry {
            return new Jerry(EntityDataHelper::parseLocation($nbt, $world),  new Skin("jerry", SkinImageParser::fromImage(imagecreatefrompng(CylexSky::getInstance()->getDataFolder() . "/EntitySkins/jerry.png"))));
        }, ["cylex:jerry"], EntityLegacyIds::PLAYER);
    }
}