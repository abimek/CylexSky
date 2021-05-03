<?php
declare(strict_types=1);

namespace cylexsky\island\creation\types;

use cylexsky\island\creation\BaseIsland;
use cylexsky\island\creation\IslandTypes;
use cylexsky\island\creation\PresetLocations;
use pocketmine\entity\Location;
use pocketmine\world\Position;

class NormalIsland extends BaseIsland implements IslandTypes, PresetLocations {

    function getPresetName(): string
    {
        return self::NORMAL;
    }

    public function getPosition(): Position{
        return new Position(0, 74, 0, $this->getWorld());
    }

    public function getLocation(): Location
    {
        return new Location(0, 74, 0, 0, 0, $this->getWorld());
    }

    public function getJamesLocation(): Location
    {
        return new Location(0, 75, 0, 0, 0, $this->getWorld());
    }

    public function getRequiredRank(): int
    {
        return self::ROOKIE;
    }
}