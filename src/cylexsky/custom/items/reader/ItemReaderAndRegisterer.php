<?php
declare(strict_types=1);

namespace cylexsky\custom\items\reader;

use core\main\data\formatter\JsonFormatter;
use customies\item\ItemComponentsTrait;
use cylexsky\custom\CustomManager;
use cylexsky\custom\extra\CylexJsonParser;
use cylexsky\custom\items\items\Armor;
use cylexsky\custom\items\items\base\CustomItem;
use cylexsky\custom\items\items\Sword;
use cylexsky\custom\items\items\traits\CustomComponentTrait;
use cylexsky\custom\items\items\traits\CustomItemTrait;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\utils\Config;
use customies\item\CustomiesItemFactory;

class ItemReaderAndRegisterer{

    use JsonFormatter;

    public const FLAGS = ["custom" => ["b", false], "creativeInventory" => ["b", true]];
    public const CLASSES = [
        "normal" => Item::class,
        "custom" => CustomItem::class,
        "sword" => Sword::class,
        "armor" => Armor::class
    ];

    public const TO_PROPER = [
        "slot.armor.head" => 2,
        "slot.armor.chestplate" => 3,
        "slot.armor.leggings" => 4,
        "slot.armor.boots" => 5
    ];

    public const FLAG_CUSTOM = "custom";
    public const FLAG_CREATIVE = "creativeInventory";

    public const REQUIRED = ["identifier", "name", "type", "flags"];

    public const PROPERTIES = "properties";
    public const COMPONENTS = "components";

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

        if (isset(class_uses($class)[CustomComponentTrait::class])){
            $d = $config->get("data");
            $data = self::readData($d, $custom);
            $properties = $data[self::PROPERTIES];
            $class::setCustomProperties($properties);
            $class::setCustomComponents($data[self::COMPONENTS]);
            $class::setInitData([$d["texture"], $d["max_stack_size"]]);
        }
        if ($config->exists("parameters") && (isset(class_uses($class)[CustomItemTrait::class]) || isset(class_uses($class)[CustomComponentTrait::class]))){
            $parameters = $config->get("parameters");
            $class::setParameters($parameters);
        }
        CustomiesItemFactory::registerItem($class, $identifier, $name);
        if ($creative){
            $item = CustomiesItemFactory::get($identifier, 1);
            CreativeInventory::getInstance()->add($item);
        }
        CustomManager::addItem(CustomiesItemFactory::get($identifier, 1));
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
        $returnData = [self::PROPERTIES => [], self::COMPONENTS => []];
        if ($custom === false){
            return $returnData;
        }
        if (isset($data["properties"])){
            $returnData[self::PROPERTIES] = $data["properties"];
            $returnData[self::COMPONENTS] = $data["components"];
        }
        foreach ($returnData[self::COMPONENTS] as $name => $component){
            $returnData[self::COMPONENTS][$name] = CylexJsonParser::parseJson(JsonFormatter::staticEncodeJson($component));
        }
        return $returnData;
    }

    private static function readFlags($data): array {
        $flags = [];
        foreach ($data as $flag => $datum){
            if (array_key_exists($flag, self::FLAGS)){
                $v = self::typeMatches($datum, self::FLAGS[$flag][0], self::FLAGS[$flag][1]);
                if ($v === null){
                    continue;
                }
                if ($v === true){
                    $flags[$flag] = $datum;
                    continue;
                }
                $flags[$flag] =  self::FLAGS[$flag][1];
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