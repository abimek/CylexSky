<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

class Boosters extends BaseModule{

    public const XP_BOOSTER_LIMIT = 1.5;
    public const FISHING_BOOSTER_LIMIT = 1.8;

    private $xpBooster = 1.0;
    private $fishingBooster = 1.0;

    public function init(array $data)
    {
        $this->xpBooster = $data[0];
        $this->fishingBooster = $data[1];
    }

    /**
     * @return float
     */
    public function getFishingBooster(): float {
        return $this->fishingBooster;
    }

    public function addFishingBooster(float $amount){
        $this->getSession()->setHasBeenChanged();
        $amount = abs($amount);
        if($this->fishingBooster < self::FISHING_BOOSTER_LIMIT){
            if ($amount > self::FISHING_BOOSTER_LIMIT - $this->fishingBooster){
                $this->fishingBooster = self::FISHING_BOOSTER_LIMIT;
            }else{
                $this->fishingBooster += $amount;
            }
        }
    }

    /**
     * @return float
     */
    public function getXpBooster(): float
    {
        return $this->xpBooster;
    }

    public function addXpBooster(float $amount){
        $this->getSession()->setHasBeenChanged();
        $amount = abs($amount);
        if($this->xpBooster < self::XP_BOOSTER_LIMIT){
            if ($amount > self::XP_BOOSTER_LIMIT - $this->xpBooster){
                $this->xpBooster = self::XP_BOOSTER_LIMIT;
            }else{
                $this->xpBooster += $amount;
            }
        }
    }

    public static function getBaseData(): array
    {
        return [1.0, 1.0];
    }

    public function save()
    {
        return $this->encodeJson([$this->xpBooster, $this->fishingBooster]);
    }
}