<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use cylexsky\island\Island;

class WealthModule{

    private $wealth;
    private $island;

    public function __construct(int $wealth, Island $island)
    {
        $this->wealth = $wealth;
        $this->island = $island;
    }

    public function getIsland(): Island{
        return $this->island;
    }

    public function getWealth(): int {
        return $this->wealth;
    }

    public function addWealth(int $amount){
        $this->island->hasBeenChanged();
        $this->wealth += $amount;
    }

    public function subtractWealth(int $amount){
        $this->island->hasBeenChanged();
        $this->wealth -= abs($amount);
    }

    public static function getBaseData(): int {
        return 0;
    }

    public function save()
    {
        return $this->wealth;
    }
}