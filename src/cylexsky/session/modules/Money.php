<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use core\main\text\TextFormat;
use cylexsky\misc\economy\EconomyManager;
use cylexsky\utils\Glyphs;
use cylexsky\utils\Utils;

class Money extends BaseModule
{

    public const BANK_MAX = 50000000;

    public const UPGRADES = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24];
    public const UPGRADES_OPALS = [2, 4, 12, 24, 40, 60, 90, 110, 170, 210, 290, 400, 500, 600, 900, 1200, 1800, 2200, 4000, 6000, 7000, 8000, 9000, 10000, 12000];
    public const AMOUNT = [1000, 2000, 5000, 10000, 20000, 40000, 80000, 160000, 200000, 500000, 1000000, 1500000, 2000000, 3000000, 6000000, 9000000, 12000000, 15000000, 17000000, 25000000, 30000000, 350000000, 400000000, 45000000, 50000000];

    private $opal;
    private $bank = 0;
    private $bankLimit;

    public function init(array $data)
    {
        $this->opal = $data[0];
        $this->bank = $data[1];
        $this->bankLimit = $data[2];
    }

    public function getNextToUpgrade(): ?int {
        if (isset(self::UPGRADES_OPALS[$this->bankLimit+1])){
            return self::UPGRADES_OPALS[$this->bankLimit+1];
        }
        return null;
    }

    public function getBankLimit(){
        return self::AMOUNT[$this->bankLimit];
    }

    public function isBankLimitMaxed(){
        if ($this->bankLimit >= count(self::UPGRADES)-1){
            return true;
        }
        return false;
    }

    public function upgradeBankLimit(){
        if ($this->bankLimit >= count(self::UPGRADES)-1){
            return;
        }
        if ($this->getOpal() < $this->getNextToUpgrade()){
            $this->getSession()->sendNotification("You need " . $this->getNextToUpgrade() . Glyphs::OPAL . "!");
            return;
        }
        $this->removeOpal($this->getNextToUpgrade());
        $this->bankLimit++;

        $this->getSession()->sendGoodNotification("Bank limit " . TextFormat::GOLD . Utils::numberToRomanRepresentation($this->bankLimit) . TextFormat::GRAY . " Unlocked!");
    }

    public function getBankValue(): int {
        return $this->bank;
    }

    public function addToBank(int $amount, bool $sendMessage = false){
        if ($this->bank >= $this->getBankLimit()){
            return;
        }
        if ($amount + $this->getBankValue() > $this->getBankLimit()){
            $amount =$this->getBankLimit() - $this->bank;
        }
        if (EconomyManager::getInstance()->myMoney($this->getSession()->getObject()->getUsername()) < $amount){
            return;
        }
        EconomyManager::getInstance()->reduceMoney($this->getSession()->getObject()->getUsername(), $amount);
        $this->bank += $amount;
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Added " . $amount . Glyphs::GOLD_COIN . " to the bank!");
    }

    public function takeFromBank(int $amount, bool $sendMessage = false){
        if ($amount > $this->bank){
            $amount = $this->bank;
        }
        $this->bank -= abs($amount);
        EconomyManager::getInstance()->addMoney($this->getSession()->getObject()->getUsername(), abs($amount));
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Taken " . $amount . Glyphs::GOLD_COIN . " from the bank!");
    }

    /**
     * @return mixed
     */
    public function getMoney()
    {
        return EconomyManager::getInstance()->myMoney($this->getSession()->getObject()->getUsername());
    }

    public function getBankLimitDirect(): int {
        return $this->bankLimit;
    }

    public function addMoney(int $amount, bool $sendMessage = false)
    {
        $this->getSession()->setHasBeenChanged();
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Added " . $amount . Glyphs::GOLD_COIN . Glyphs::BOX_EXCLAMATION);
        EconomyManager::getInstance()->addMoney($this->getSession()->getObject()->getUsername(), $amount);
    }

    public function removeMoney(int $amount, bool $sendMessage = false)
    {
        $this->getSession()->setHasBeenChanged();
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Removed " . $amount . Glyphs::GOLD_COIN . Glyphs::BOX_EXCLAMATION);
        EconomyManager::getInstance()->reduceMoney($this->getSession()->getObject()->getUsername(), $amount);
    }

    /**
     * @return int
     */
    public function getOpal(): int
    {
        return $this->opal;
    }

    public function addOpal(int $amount, bool $sendMessage = false)
    {
        $this->getSession()->setHasBeenChanged();
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Added " . $amount . Glyphs::OPAL . Glyphs::BOX_EXCLAMATION);
        $this->opal += $amount;
    }

    public function removeOpal(int $amount, bool $sendMessage = false)
    {
        $this->getSession()->setHasBeenChanged();
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Removed " . $amount . Glyphs::OPAL . Glyphs::BOX_EXCLAMATION);
        $this->opal -= abs($amount);
    }

    public function getCurrentDipositiableAmount(): int {
        if ($this->bank >= $this->getBankLimit()){
            return 0;
        }
        $money = $this->getMoney();
        $a = $this->getBankValue();
        if ($this->getBankLimit() - $a > $money){
            return intval($money);
        }
        return intval($this->getBankLimit() - $a);
    }

    public static function getBaseData(): array
    {
        return [50, 0, 0];
    }

    public function save(): string
    {
        return $this->encodeJson([$this->opal, $this->bank, $this->bankLimit]);
    }

}