<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use core\main\text\TextFormat;
use cylexsky\utils\Glyphs;
use cylexsky\utils\Sounds;
use cylexsky\utils\Utils;

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
        $romanPreviousLevel = Utils::numberToRomanRepresentation($previousLevel);
        $this->level++;
        $l = Utils::numberToRomanRepresentation($this->level);
        $this->getSession()->getPlayer()->sendMessage(TextFormat::BOLD_AQUA . "+++++++++++++++++++++++");
        $this->getSession()->getPlayer()->sendMessage(TextFormat::BOLD_AQUA . " Player Level Up" . TextFormat::RESET_GRAY . $romanPreviousLevel . Glyphs::RIGHT_ARROW . TextFormat::AQUA . $l);
        $this->getSession()->getPlayer()->sendMessage(TextFormat::WHITE . " Rewards:");
        if ($this->level % 5 === 0){
            $this->getSession()->getMoneyModule()->addOpal($this->getLevel(), false);
            $this->getSession()->getPlayer()->sendMessage("  " . Glyphs::OPAL . TextFormat::GRAY . ": " . TextFormat::GOLD . $this->getLevel());
        }
        Sounds::sendSoundPlayer($this->getSession()->getPlayer(), Sounds::LEVEL_UP_SOUND);
        $this->getSession()->getMoneyModule()->addMoney($this->getLevel() * 25, false);
        $this->getSession()->getPlayer()->sendMessage("  " . Glyphs::GOLD_COIN . TextFormat::GRAY . ": " . TextFormat::GOLD . $this->getLevel() * 25);
        $this->getSession()->getPlayer()->sendMessage(TextFormat::BOLD_AQUA . "+++++++++++++++++++++++");
        $this->getSession()->getPlayer()->sendPopup(Glyphs::GOLD_MEDAL . TextFormat::GOLD . "You've leveled up!" . Glyphs::GOLD_MEDAL);
        $this->getSession()->getPlayer()->sendSubTitle(Glyphs::SPARKLE . TextFormat::BOLD_GREEN . $romanPreviousLevel . " " . TextFormat::BOLD_GOLD . Glyphs::RIGHT_ARROW .  " " .$l . Glyphs::SPARKLE);
    }

    public function calculateLevel(){
        if ($this->level >= self::MAX_LEVEL){ return;}
        if ($this->xp >= $this->getXpForNextLevel()){
            $this->addLevel();
        }
    }

    public function getXpForNextLevel(){
        return floor(pow($this->level, 2.8) * 20);
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