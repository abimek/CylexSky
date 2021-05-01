<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use pocketmine\entity\Location;
use pocketmine\world\World;

class TeleportsModule extends BaseModule{

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

    public function deleteWarp(string $name): bool {
        if (isset($this->warps[$name])){
            unset($this->warps[$name]);
            return true;
        }
        return false;
    }

    public function addWarp(string $name, Location $location){
        if (count($this->warps) >= $this->getIsland()->getUpgradeModule()->getWarpLimit()){
            return;
        }
        $this->warps[$name] = $location;
    }

    public function canAddWarp(): bool {
        if (count($this->warps) >= $this->getIsland()->getUpgradeModule()->getWarpLimit()){
            return false;
        }
        return true;
    }

    public function getWarp(string $name): ?Location{
        if ($this->warpExists($name)){
            return $this->warps[$name];
        }
        return null;
    }

    public function warpExists(string $name){
        return isset($this->warps[$name]);
    }

    public function getWarpCount(): int {
        return count($this->warps);
    }

    public function teleport(string $warp, PlayerSession $session){
        if (!$this->warpExists($warp)){
            $session->sendIslandMessage(TextFormat::GOLD . $warp . TextFormat::GRAY . " is not a warp!");
            return;
        }
        if (!$session->getTeleportModule()->canTeleport()){
            $session->sendNotification(TextFormat::RED . "Unable to teleport!");
            return;
        }
        $this->getIsland()->loadWorld();
        $l = $this->warps[$warp];
        $location = new Location($l->getX(), $l->getY(), $l->getZ(), $l->yaw, $l->pitch, $this->getIsland()->getWorld());
        $session->getPlayer()->teleport($location, $l->yaw, $l->pitch);
        $session->sendIslandMessage(TextFormat::GRAY . "Teleporting to warp " . TextFormat::GOLD . $warp . TextFormat::GRAY . "!");
    }



    public function getWarps(): array {
        return $this->warps;
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