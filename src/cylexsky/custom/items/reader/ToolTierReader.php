<?php
declare(strict_types=1);

namespace cylexsky\custom\items\reader;

use pocketmine\item\ToolTier;
use pocketmine\utils\Config;

class ToolTierReader{

    public static function readToolTiers(string $directory){
        $config = new Config($directory, Config::JSON);
        foreach ($config->getAll() as $name => $data){
            self::readAndRegisterToolTier($name, $data);
        }
    }

    public static function readAndRegisterToolTier(string $name, array $data){
        $harvest_level = $data["harvest_level"];
        $max_durability = $data["max_durability"];
        $base_attack_points = $data["base_attack_points"];
        $base_efficiency = $data["base_efficiency"];
        $reflectionMethod = new \ReflectionMethod(ToolTier::class, "register");
        $reflectionMethod->setAccessible(true);

        $class = new \ReflectionClass(ToolTier::class);
        $constructor = $class->getConstructor();
        $constructor->setAccessible(true);
        $object = $class->newInstanceWithoutConstructor();
        $constructor->invokeArgs($object, [$name, $harvest_level, $max_durability, $base_attack_points, $base_efficiency]);
        $reflectionMethod->invoke(null, $object);
    }
}