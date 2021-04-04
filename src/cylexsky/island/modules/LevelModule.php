<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use core\main\text\TextFormat;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use cylexsky\utils\Sounds;
use cylexsky\utils\Utils;
use pocketmine\player\Player;
use pocketmine\Server;

class LevelModule extends BaseModule{

    public const PRESTIEGE_MAX = 11;
    public const MAX_LEVEL = 110;

    private $level;
    private $xp;

    private $prestige;

    public function init(array $data)
    {
        $this->level = $data[0];
        $this->prestige = $data[1];
        $this->xp = $data[2];
    }

    public function getLevel(): int {
        return $this->level;
    }

    public function getXp(){
        return  $this->xp;
    }

    public function getPrestige(){
        return $this->prestige;
    }

    public function prestige(){
        $p = Utils::numberToRomanRepresentation($this->prestige);
        if (!$this->hasEnoughPrestigeShardsToPrestige()){
            return;
        }
        $this->getIsland()->subtractPrestigeShards($this->getPrestigeShardsForNextLevel());
        $this->prestige++;
        $l = Utils::numberToRomanRepresentation($this->prestige);
        foreach ($this->getIsland()->getMembersModule()->getOnlineMembers() as $member) {
            if (!$member instanceof Player) {
                continue;
            }
            $member->sendMessage(Glyphs::JERRY_LINE_1 . TextFormat::GRAY . "Wonderful job prestiging you island!");
            $member->sendMessage(Glyphs::JERRY_LINE_2 . TextFormat::GRAY . "You've unlocked more island upgrades!");
            $member->sendMessage(Glyphs::JERRY_LINE_3 . TextFormat::GRAY . TextFormat::BOLD_GOLD . "Prestige" . TextFormat::RESET_DARK_GRAY . $p . Glyphs::RIGHT_ARROW . TextFormat::AQUA . $l);
        }
    }

    private function addLevel(){
        $this->xp = 0;
        $previousLevel = $this->level;
        $this->level++;
        $romanPreviousLevel = Utils::numberToRomanRepresentation($previousLevel);
        $l =  Utils::numberToRomanRepresentation($this->level);
        $prestigeShards = $this->level * 3;
        $this->getIsland()->addPrestigeShards($prestigeShards);
        foreach ($this->getIsland()->getMembersModule()->getOnlineMembers() as $member) {
            if (!$member instanceof Player) {
                continue;
            }
            $coins = $this->level * 20;
            $xp = $this->level * 120;
            $opals = $this->level * 8;
            Sounds::sendSoundPlayer($member, Sounds::ISLAND_LEVEL_UP_SOUND);
            $member->sendMessage(TextFormat::YELLOW . "■■■■■■■■■■■■■■■■■■■■■■■■■■");
            $member->sendMessage(TextFormat::BOLD_GOLD . " Island Level Up " . TextFormat::YELLOW . $romanPreviousLevel . Glyphs::RIGHT_ARROW . TextFormat::AQUA . $l . TextFormat::GOLD);
            $member->sendMessage(TextFormat::BOLD_GREEN . " Rewards:");
            $member->sendMessage(TextFormat::AQUA . "  Prestige Shards:" . TextFormat::YELLOW . $prestigeShards);
            $member->sendMessage(TextFormat::WHITE . "   Unlocks different island levelups!");
            $member->sendMessage(TextFormat::DARK_GRAY . "   +" . TextFormat::GOLD . $coins . Glyphs::GOLD_COIN);
            $member->sendMessage(TextFormat::DARK_GRAY . "   +" . TextFormat::GOLD . $opals . Glyphs::OPAL);
            $member->sendMessage(TextFormat::DARK_GRAY . "   +" . TextFormat::GOLD . $xp . TextFormat::GRAY . "Xp");
            $member->sendMessage(TextFormat::YELLOW . "■■■■■■■■■■■■■■■■■■■■■■■■■■");
            $session = SessionManager::getSession($member->getXuid());
            $session->getMoneyModule()->addMoney($coins, false);
            $session->getMoneyModule()->addOpal($opals, false);
            $session->getLevelModule()->addXp($xp);
        }
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $player->sendMessage(TextFormat::YELLOW . $romanPreviousLevel . Glyphs::RIGHT_ARROW . TextFormat::AQUA . $l . TextFormat::GOLD . $this->getIsland()->getOwnerName() . "'s " . TextFormat::AQUA . "island leveled up!");
        }
    }

    public function calculateLevel(){
        if ($this->level >= self::MAX_LEVEL){ return;}
        if ($this->xp >= $this->getXpForNextLevel()){
            $this->addLevel();
        }
    }

    public function canPrestige(): bool {
        if ($this->prestige === self::PRESTIEGE_MAX){
            return false;
        }
        if (!$this->hasEnoughLevelToPrestige()){
            return false;
        }
        if (!$this->hasEnoughPrestigeShardsToPrestige()){
            return false;
        }
        return true;
    }

    public function hasEnoughLevelToPrestige(): bool {
        if ($this->prestige === self::PRESTIEGE_MAX){
            return false;
        }
        return ($this->prestige * 10 <= $this->level);
    }

    public function hasEnoughPrestigeShardsToPrestige(): bool {
        if ($this->prestige === self::PRESTIEGE_MAX) {
            return false;
        }
        return ($this->getPrestigeShardsForNextLevel() <= $this->getIsland()->getPrestigeShards());
    }

    public function getNextPrestigeLevel(){
        return $this->prestige + 1 * 10;
    }

    public function getPrestigeShardsForNextLevel(): int {
        return intval(floor(pow(($this->prestige + 1), 2.3)*50));
    }

    public function getXpForNextLevel(){
        return floor(pow($this->level, 2.5) * 70);
    }

    public function addXp(int $amount)
    {
        if ($this->level >= self::MAX_LEVEL) {
            return;
        }
        $this->xp += abs($amount);
        $this->calculateLevel();
    }

    public static function getBaseData(): array
    {
        return [1, 1, 0];
    }

    public function save()
    {
        return $this->encodeJson([$this->level, $this->prestige, $this->xp]);
    }
}