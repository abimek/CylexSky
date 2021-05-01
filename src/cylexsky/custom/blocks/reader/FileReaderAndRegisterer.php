<?php
declare(strict_types=1);

namespace cylexsky\custom\blocks\reader;

use customies\block\Material;
use cylexsky\custom\blocks\blocks\Chair;
use cylexsky\custom\blocks\blocks\Chair17;
use cylexsky\custom\blocks\blocks\NormalBlock;
use cylexsky\custom\blocks\blocks\RotatableBlock;
use cylexsky\custom\blocks\blocks\traits\ParameterTrait;
use cylexsky\custom\CustomManager;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockToolType;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use customies\block\Model;

use customies\block\CustomiesBlockFactory;

class FileReaderAndRegisterer{

    public const FLAGS = ["custom" => ["b", false], "creativeInventory" => ["b", true]];
    public const CLASSES = [
        "normal" => NormalBlock::class,
        "chair" => Chair::class,
        "chair17" => Chair17::class,
        "rotatable" => RotatableBlock::class
    ];

    public const FLAG_CUSTOM = "custom";
    public const FLAG_CREATIVE = "creativeInventory";

    public const MODEL = "model";

    public const REQUIRED = ["identifier", "name", "type", "flags"];

    public static function read(string $file)
    {
        $config = new Config($file, Config::JSON);
        self::checkRequirements($config);
        $identifier = $config->get("identifier");
        $name = $config->get("name");
        $type = $config->get("type");
        $class = self::readType($type);
        $flags = self::readFlags($config->get("flags"));
        $custom = $flags[self::FLAG_CUSTOM];
        $creative = $flags[self::FLAG_CREATIVE];
        $model = null;
        if ($custom){
            $data = self::readData($config->get("data"), $custom);
            $model = $data[self::MODEL];
        }
        $breakInfo = $class::getTrueBreakInfo();
        CustomiesBlockFactory::registerBlock($class, $identifier, $name, $breakInfo, $model);
        $block = CustomiesBlockFactory::get($identifier);
        if ($block instanceof Block){
            $item = $block->asItem();
            $item->setCount(64);
            CustomManager::addItem($item);
        }
        if ($creative){
            $block = CustomiesBlockFactory::get($identifier);
            if ($block instanceof Block){
                $item = $block->asItem();
                CreativeInventory::getInstance()->add($item);
            }
        }
    }

    private static function checkRequirements(Config $blocks){
        $data = $blocks->getAll();
        foreach (self::REQUIRED as $value){
            if (!isset($data[$value])){
                throw new \Exception($value . " field is missing");
            }
        }
    }

    private static function readData(array $data, bool $custom): array {
        $returnData = [self::MODEL => null];
        if ($custom === false){
            return $returnData;
        }
        if (!isset($data["custom"])){
            throw new \Exception("The custom field is required for data if the custom flag is enabled!");
        }
        $data = $data["custom"];
        $returnData[self::MODEL] = new Model([new Material("*", $data["texture"], $data["render_method"])], $data["geometry"], self::getVector3($data["origin"]), self::getVector3($data["size"]));
        return $returnData;
    }

    private static function getVector3(array $array){
        return new Vector3($array[0], $array[1], $array[2]);
    }

    private static function getDefaultBreakInfo(): BlockBreakInfo{
        return new BlockBreakInfo(1.25, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 21.0);
    }

    private static function readFlags($data): array {
        $flags = [];
        foreach ($data as $flag => $datum){
            if (array_key_exists($flag, self::FLAGS)){
                $v = self::typeMatches($datum, self::FLAGS["custom"][0], self::FLAGS["custom"][1]);
                if ($v === null){
                    continue;
                }
                if ($v === true){
                    $flags[$flag] = $datum;
                    continue;
                }
                $flags[$flag] =  self::FLAGS["custom"][1];
            }
        }
        foreach (array_keys(self::FLAGS) as $flag => $data){
            if (!isset($flags[$flag])){
                $flags[$flag] = $data[1];
            }
        }
        return $flags;
    }

    private static function typeMatches($type, string $shouldBeType, $default){
        switch ($shouldBeType){
            case "b":
                return is_bool($type);
            case "i":
                return is_int($type);
            case "f":
                return is_float($type);
            case "s":
                return is_string($type);
        }
        return null;
    }

    private static function readType(string $type): string {
        if (!array_key_exists($type, self::CLASSES)){
            throw new \Exception("The block class $type does not exist!");
        }
        return self::CLASSES[$type];
    }
}