<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use core\main\text\TextFormat;
use cylexsky\utils\Glyphs;

class Level extends BaseModule {

    public const MAX_LEVEL = 111;

    private $level;
    private $xp;

    public function init(array $data)
    {
        $this->level = $data[0];
        $this->xp = $data[1];
    }

    public function getLevel(): int {
        return $this->level;
    }

    public function getXp(): int {
        return $this->xp;
    }

    private function addLevel(){
        $this->xp = 0;
        $previousLevel = $this->level;
        $this->level++;
        if ($this->level % 5 === 0){
            $this->getSession()->getMoneyModule()->addOpal($this->getLevel());
            $this->getSession()->getMoneyModule()->addMoney($this->getLevel() * 3);
        }
        $this->getSession()->getPlayer()->sendPopup(Glyphs::GOLD_MEDAL . TextFormat::GOLD . "You've leveled up!" . Glyphs::GOLD_MEDAL);
        $this->getSession()->getPlayer()->sendSubTitle(Glyphs::SPARKLE . TextFormat::RED . $previousLevel . " " . TextFormat::GRAY . Glyphs::RIGHT_ARROW .  " " .$this->level . Glyphs::SPARKLE);
    }

    public function calculateLevel(){
        if ($this->level >= self::MAX_LEVEL){ return;}
        if ($this->xp >= $this->getXpForNextLevel()){
            $this->addLevel();
        }
    }

    public function getXpForNextLevel(){
        return floor(pow($this->level, 1.4) * 60);
    }

    public function addXp(int $amount){
        if ($this->level >= self::MAX_LEVEL){return;}
        $this->xp += abs($amount);
        $this->calculateLevel();
    }

    public static function getBaseData(): array
    {
        return [1, 0];
    }

    public function save(): string
    {
        return $this->encodeJson([$this->level, $this->xp]);
    }
}