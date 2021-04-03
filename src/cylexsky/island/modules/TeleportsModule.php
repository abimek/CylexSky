<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use pocketmine\entity\Location;
use pocketmine\world\World;

class TeleportsModule extends BaseModule{

    public const MAX_WARPS = 7;

    private $warpsLimit = 1;

    private $warps = [];
    private $trustedSpawn;

    public function init(array $data)
    {
        if ($data[0] === null){
         //   $l = $this->getIsland()->getSpawnLocation();
           // $this->trustedSpawn = new Location($l->getX(), $l->getY(), $l->getZ(), 0, 0, $this->getIsland()->getWorld());
        }else{
            $this->trustedSpawn = self::decodeLocation($data[0],  $this->getIsland()->getWorld());
        }
        foreach ($data[1] as $name => $location){
            $this->warps[$name] = self::decodeLocation($location, $this->getIsland()->getWorld());
        }
       // $this->warps = $data[1];
    }

    public function setTrustedSpawn(Location $location){
        $this->getIsland()->setHasBeenChanged();
        $this->trustedSpawn = $location;
    }

    public function getTrustedSpawn(): Location{
        return $this->trustedSpawn;
    }

    public static function getBaseData(): array
    {
        return [null, []];
    }


    public function save()
    {
        $l = $this->trustedSpawn;
        $array = [];
        foreach ($this->warps as $name => $location){
            $array[$name] = self::encodeLocation($location);
        }
        return $this->encodeJson([self::encodeLocation($l), $array]);
    }

    public static function encodeLocation(Location $location): array {
        return [$location->getX(), $location->getY(), $location->getZ(), $location->yaw, $location->pitch];
    }

    public static function decodeLocation(array $data, ?World $world): Location{
        return new Location($data[0], $data[1], $data[2], $data[3], $data[4], $world);
    }
}