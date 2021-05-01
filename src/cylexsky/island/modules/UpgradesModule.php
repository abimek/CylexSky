<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use core\main\text\TextFormat;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class UpgradesModule extends BaseModule{

    private $data;

    public const UPGRADE_AMOUNT = 6;

    public const MINION_LIMIT = 0;
    public const SPAWNER_LIMIT = 1;
    public const SPAWNER_TYPES = 2;
    public const RESOURCE_NODE_LIMIT = 3;
    public const RESOURCE_NODE_TYPE = 4;
    public const HOPPER_LIMIT = 5;
    public const WARPS_LIMIT = 6;
    public const ISLAND_BORDER = 7;

    public const PRESTIGE_REQUIRED_POINTS = [3, 8, 14, 17, 23, 27, 36, 43, 49, 60, 71];

    public const MINION_LIMITS = [3, 6, 9, 11, 13, 15, 17, 20, 22, 24, 26];
    public const SPAWNER_LIMITS = [5, 20, 30, 40, 100, 150, 200, 250, 300, 350, 1500];
    public const SPAWNER_TYPE_LIST = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    public const RESOURCE_NODE_LIMITS = [10, 20, 30, 65, 80, 120, 180, 240, 300, 350, 450];
    public const RESOURCE_NODE_TYPES_LIST = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    public const HOPPER_LIMITS = [10, 20, 35, 60, 80, 110, 120, 140, 180, 240, 270];
    public const WARP_LIMITS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
    public const ISLAND_BORDERS = [32, 48, 70, 150, 250, 350, 450, 550, 650, 750, 1000];

    public const RESOURCE_NODE_NAMES = [
        "Wooden Resource Node",
        "Stone Resource Node",
        "Coal Resource Node",
        "Lapis Resource Node",
        "Redstone Resource Node",
        "Quartz Resource Node",
        "Iron Resource Node",
        "Gold Resource Node",
        "Gold Resource Node",
        "Emerald Resource Node",
        "Netherite Resource Node"
    ];
    
    public const SPAWNER_TYPE_NAMES = [
        "Chicken Spawner",
        "Pig Spawner",
        "Cow Spawner",
        "Spider Spawner",
        "Zombie Spawner",
        "Creeper Spawner",
        "Zombie Pigman Spawner",
        "Blaze Spawner",
        "Enderman Spawner",
        "Iron Golem Spawner",
        "Mini Wither Spawner"
    ];

    public function init(array $data)
    {
        $this->data = $data;
    }

    public function getMinionLimit(): int {return self::MINION_LIMITS[$this->data[self::MINION_LIMIT]];}
    public function getSpawnerLimit(): int {return self::SPAWNER_LIMITS[$this->data[self::SPAWNER_LIMIT]];}
    public function getSpawnerType(): int {return self::SPAWNER_TYPE_LIST[$this->data[self::SPAWNER_TYPES]];}
    public function getResourceNodeLimit(): int {return self::RESOURCE_NODE_LIMITS[$this->data[self::RESOURCE_NODE_LIMIT]];}
    public function getResourceNodeType(): int {return self::RESOURCE_NODE_TYPES_LIST[$this->data[self::RESOURCE_NODE_TYPE]];}
    public function getHopperLimit(): int {return self::HOPPER_LIMITS[$this->data[self::HOPPER_LIMIT]];}
    public function getWarpLimit(): int {return self::WARP_LIMITS[$this->data[self::WARPS_LIMIT]];}
    public function getIslandBorderLimit(): int{return self::ISLAND_BORDERS[$this->data[self::ISLAND_BORDER]];}

    public function getNextMinionLimit(): ?int {
        $d = $this->data[self::MINION_LIMIT]+1;
        if (isset(self::MINION_LIMITS[$d])){
            return self::MINION_LIMITS[$d];
        }
        return null;
    }

    public function getNextSpawnerLimit(): ?int{
        $d = $this->data[self::SPAWNER_LIMIT]+1;
        if (isset(self::SPAWNER_LIMITS[$d])){
            return self::SPAWNER_LIMITS[$d];
        }
        return null;
    }

    public function getNextSpawnerType(): ?int{
        $d = $this->data[self::SPAWNER_TYPES]+1;
        if (isset(self::SPAWNER_TYPE_LIST[$d])){
            return self::SPAWNER_TYPE_LIST[$d];
        }
        return null;
    }

    public function getNextResourceNodeLimit(): ?int{
        $d = $this->data[self::RESOURCE_NODE_LIMIT]+1;
        if (isset(self::RESOURCE_NODE_LIMITS[$d])){
            return self::RESOURCE_NODE_LIMITS[$d];
        }
        return null;
    }

    public function getNextResourceNodeType(): ?int{
        $d = $this->data[self::RESOURCE_NODE_TYPE]+1;
        if (isset(self::RESOURCE_NODE_TYPES_LIST[$d])){
            return self::RESOURCE_NODE_TYPES_LIST[$d];
        }
        return null;
    }

    public function getNextHopperLimit(): ?int{
        $d = $this->data[self::HOPPER_LIMIT]+1;
        if (isset(self::HOPPER_LIMITS[$d])){
            return self::HOPPER_LIMITS[$d];
        }
        return null;
    }

    public function getNextWarpLimit(): ?int{
        $d = $this->data[self::WARPS_LIMIT]+1;
        if (isset(self::WARP_LIMITS[$d])){
            return self::WARP_LIMITS[$d];
        }
        return null;
    }

    public function getNextBorderLimit(): ?int {
        $d = $this->data[self::ISLAND_BORDER]+1;
        if (isset(self::ISLAND_BORDERS[$d])){
            return self::ISLAND_BORDERS[$d];
        }
        return null;
    }

    public function isCurrentPrestigeMax(int $data): bool {
        $prestige = $this->getIsland()->getLevelModule()->getPrestige() - 1;
        if ($this->data[$data] === $prestige){
            return true;
        }else{
            return false;
        }
    }

    public function getNextPrestigeUpgrade(int $data): ?int {
        $prestige = $this->getIsland()->getLevelModule()->getPrestige();
        $array = [];
        switch ($data){
            case 0:$array = self::MINION_LIMITS;break;
            case 1:$array = self::SPAWNER_LIMITS;break;
            case 2:$array = self::SPAWNER_TYPES;break;
            case 3:$array = self::RESOURCE_NODE_LIMITS;break;
            case 4:$array = self::RESOURCE_NODE_TYPES_LIST;break;
            case 5:$array = self::HOPPER_LIMITS;break;
            case 6:$array = self::WARPS_LIMIT;break;
            case 7:$array = self::ISLAND_BORDERS;break;
        }
        if (isset($array[$prestige])){
            return $prestige + 1;
        }
        return null;
    }

    public function upgrade(int $data){
        $prestige = $this->getIsland()->getLevelModule()->getPrestige();
        if ($this->canUpgrade($data)){
            if ($prestige === 1){
                return;
            }
            if (self::PRESTIGE_REQUIRED_POINTS[$prestige-1] >= $this->getIsland()->getPrestigePoints()){
                return;
            }
            if (!$this->hasEnoughPrestigeCoinsForNextUpgrade($data)){
                return;
            }
            $this->getIsland()->subtractPrestigePoints(self::PRESTIGE_REQUIRED_POINTS[$prestige-1]);
            $this->data[$data]++;
            $this->sendUpgradedMessage($data);
        }
    }

    public function sendUpgradedMessage(int $data){
        $array = [];
        switch ($data){
            case 0:$array = self::MINION_LIMITS;break;
            case 1:$array = self::SPAWNER_LIMITS;break;
            case 2:$array = self::SPAWNER_TYPE_LIST;break;
            case 3:$array = self::RESOURCE_NODE_LIMITS;break;
            case 4:$array = self::RESOURCE_NODE_TYPES_LIST;break;
            case 5:$array = self::HOPPER_LIMITS;break;
            case 6:$array = self::WARPS_LIMIT;break;
            case 7:$array = self::ISLAND_BORDERS;break;
        }
        if (empty($array)){
            return;
        }
        $prestige = $this->getIsland()->getLevelModule()->getPrestige();
        $previous = $array[$prestige - 2];
        $new = $array[$this->data[$data]];
        foreach ($this->getIsland()->getMembersModule()->getOnlineMembers() as $member){
            if (!$member instanceof Player){
                continue;
            }
            $member->sendMessage(str_repeat(Glyphs::LINE, 23));
            $member->sendMessage(Glyphs::ISLAND_ICON . TextFormat::BOLD_GOLD . "Island Upgrade: ");
            $accomplished = false;
            switch ($data){
                case self::MINION_LIMIT:
                    $accomplished = true;
                    $member->sendMessage(TextFormat::RESET_AQUA . "  " . "Minion Limit: ");
                    break;
                case self::SPAWNER_LIMIT:
                    $accomplished = true;
                    $member->sendMessage(TextFormat::RESET_AQUA . "  " . "Spawner Limit: ");
                    break;
                case self::SPAWNER_TYPES:
                    $accomplished = false;
                    $member->sendMessage(TextFormat::RESET_AQUA . "  " . "Spawner Type: ");
                    break;
                case self::RESOURCE_NODE_LIMITS:
                    $accomplished = true;
                    $member->sendMessage(TextFormat::RESET_AQUA . "  " . "Resource Node Limit: ");
                    break;
                case self::HOPPER_LIMIT:
                    $accomplished = true;
                    $member->sendMessage(TextFormat::RESET_AQUA . "  " . "Hopper Limit: ");
                    break;
                case self::WARPS_LIMIT:
                    $accomplished = true;
                    $member->sendMessage(TextFormat::RESET_AQUA . "  " . "Warp Limit: ");
                    break;
                case self::ISLAND_BORDER:
                    $accomplished = false;
                    $member->sendMessage(TextFormat::RESET_AQUA . "  " . "Island Border Limit: ");
                    break;
            }
            if ($accomplished){
                $member->sendMessage(TextFormat::YELLOW . "    " . $previous . Glyphs::RIGHT_ARROW . TextFormat::GREEN . $new);
                $member->sendMessage(str_repeat(Glyphs::LINE, 10));
                continue;
            }
            switch ($data){
                case self::SPAWNER_TYPES:
                    $name = self::SPAWNER_TYPE_NAMES[$this->data[$data]];
                    $member->sendMessage(TextFormat::RED . "    " . $name . TextFormat::GOLD . " Spawner " . TextFormat::GREEN . "Unlocked!");
                    break;
                case self::RESOURCE_NODE_TYPE:
                    $name = self::RESOURCE_NODE_NAMES[$this->data[$data]];
                    $member->sendMessage(TextFormat::GOLD . "    $name " . TextFormat::GREEN . "Unlocked!");
                    break;
                case self::ISLAND_BORDER:
                    $name = self::ISLAND_BORDERS[$this->data[$data]];
                    $name = TextFormat::AQUA . $name . TextFormat::GRAY . "x" . TextFormat::AQUA . $name . TextFormat::GRAY . " border ";
                    $member->sendMessage(TextFormat::GOLD . "    $name " . TextFormat::GREEN . "Unlocked!");
                    break;
            }
            $member->sendMessage(str_repeat(Glyphs::LINE, 10));
        }
    }

    public function canUpgrade(int $data): bool {
        $prestige = $this->getIsland()->getLevelModule()->getPrestige();
        $prestige--;
        if ($prestige === $this->data[$data]){
            return false;
        }
        if ($prestige > $this->data[$data]){
            return true;
        }
        return false;
    }


    public function hasEnoughPrestigeCoinsForNextUpgrade(int $data){
        $prestige = $this->getIsland()->getLevelModule()->getPrestige();
        if ($this->canUpgrade($data)){
            if (isset(self::PRESTIGE_REQUIRED_POINTS[$prestige-1])){
                if (self::PRESTIGE_REQUIRED_POINTS[$prestige-1] < $this->getIsland()->getPrestigePoints()){
                    return true;
                }
            }
        }
        return false;
    }

    public function getPrestigeCoinsForNextUpgrade(int $data): ?int{
        $prestige = $this->getIsland()->getLevelModule()->getPrestige();
        if ($this->canUpgrade($data)){
            if (isset(self::PRESTIGE_REQUIRED_POINTS[$prestige-1])){
                return self::PRESTIGE_REQUIRED_POINTS[$prestige-1];
            }
        }
        return null;
    }

    public static function getBaseData(): array
    {
        return [self::MINION_LIMIT => 0, self::SPAWNER_LIMIT => 0, self::SPAWNER_TYPES => 0, self::RESOURCE_NODE_LIMIT => 0, self::RESOURCE_NODE_TYPE => 0, self::HOPPER_LIMIT => 0, self::WARPS_LIMIT => 0, self::ISLAND_BORDER => 0];
    }

    public function save()
    {
        return $this->encodeJson($this->data);
    }
}