<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use core\main\text\TextFormat;
use cylexsky\utils\Glyphs;

class Money extends BaseModule
{

    private $money;
    private $silver;

    public function init(array $data)
    {
        $this->money = $data[0];
        $this->silver = $data[1];
    }

    /**
     * @return mixed
     */
    public function getMoney()
    {
        return $this->money;
    }

    public function addMoney(int $amount, bool $sendMessage = false)
    {
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Added " . $amount . Glyphs::GOLD_COIN . Glyphs::BOX_EXCLAMATION);
        $this->money += $amount;
    }

    public function removeMoney(int $amount, bool $sendMessage = false)
    {
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Removed " . $amount . Glyphs::GOLD_COIN . Glyphs::BOX_EXCLAMATION);
        $this->money -= abs($amount);
    }

    /**
     * @return mixed
     */
    public function getSilver()
    {
        return $this->silver;
    }

    public function addSilver(int $amount, bool $sendMessage = false)
    {
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Added " . $amount . Glyphs::SILVER_COINS . Glyphs::BOX_EXCLAMATION);
        $this->silver += $amount;
    }

    public function removeSilver(int $amount, bool $sendMessage = false)
    {
        if ($sendMessage) $this->getSession()->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Removed " . $amount . Glyphs::SILVER_COINS . Glyphs::BOX_EXCLAMATION);
        $this->silver -= abs($amount);
    }

    public static function getBaseData(): array
    {
        return [500, 50];
    }

    public function save(): string
    {
        return $this->encodeJson([$this->money, $this->silver]);
    }

}