<?php
declare(strict_types=1);

namespace cylexsky\misc\shop\objects;

use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\item\Item;
use pocketmine\player\Player;

class ShopItem{

    public const COINS = "coins";
    public const OPALS = "opals";
    public const XP = "xp";

    private $item;

    private $name;

    private $buyPrice = null;
    private $sellPrice = null;

    private $currency;

    private $buttonName;

    private $texture;

    public function __construct(array $data)
    {
        if (isset($data["buyPrice"])){
            $this->buyPrice = intval($data["buyPrice"]);
        }
        if (isset($data["sellPrice"])){
            $this->sellPrice = intval($data["sellPrice"]);
        }
        if (isset($data["texture"])){
            $this->texture = $data["texture"];
        }
        $this->item = Item::jsonDeserialize($data["item"]);
        $this->buttonName = $data["buttonName"];
        $this->currency = $data["currency"];
        $this->name = $data["name"];
    }

    public function buyItems(int $amount, ?Player $player = null): ?int {
        if ($this->buyPrice === null){
            if ($player !== null){
                SessionManager::getSession($player->getXuid())->sendShopMessage("Item is not purchasable");
            }
            return null;
        }
        $item = clone $this->getItem();
        $item->setCount($item->getCount() * $amount);
        $price = $this->getBuyPrice($amount);
        if ($player !== null){
            $session = SessionManager::getSession($player->getXuid());
            if (!$player->getInventory()->canAddItem($item)){
                $session->sendShopMessage("You do not have enough inventory space for " . $this->format($item->getCount()) . "!");
                return null;
            }
            if (!$this->hasAmount($session, $price)){
                $session->sendShopMessage("You need " . TextFormat::RED . $price . $this->currencyToGlyphOrString() . TextFormat::GRAY . "to purchase " . $this->format($item->getCount()) . TextFormat::GRAY . "!");
                return null;
            }
            $this->reduceAmount($session, $price);
            $player->getInventory()->addItem($item);
            $session->sendShopMessage("Successfully purchased " . $this->format($item->getCount()) . " for " . TextFormat::GOLD . $price . $this->currencyToGlyphOrString() . TextFormat::GRAY . "!");
            return $price;
        }
        //TODO WHEN NEEDED
    }

    public function getTexture(): ?string {
        return $this->texture;
    }

    public function sellItems(int $amount, ?Player $player = null){
        if ($this->sellPrice === null){
            if ($player !== null){
                SessionManager::getSession($player->getXuid())->sendShopMessage("Item is not sellable");
            }
            return;
        }
        $item = clone $this->getItem();
        $item->setCount($item->getCount() * $amount);
        $price = $this->getSellPrice($amount);
        if ($player !== null){
            $session = SessionManager::getSession($player->getXuid());
            if (!$player->getInventory()->contains($item)){
                $session->sendShopMessage("You do not have " . $this->format($item->getCount()) . "!");
                return null;
            }
            $session->sendShopMessage("Successfully sold " . $this->format($item->getCount()) . " for " . TextFormat::GOLD . $price . $this->currencyToGlyphOrString() . TextFormat::GRAY . "!");
            $this->addAmount($session, $price);
            $player->getInventory()->removeItem($item);
            return $price;
        }
    }

    public function currencyToGlyphOrString(): string {
        switch ($this->getCurrency()){
            case self::XP:
                return "xp";
            case self::OPALS:
                return Glyphs::OPAL;
            case self::COINS:
                return Glyphs::GOLD_COIN;
        }
        throw new \Exception("SHOP BRGIOUIHSGE");
    }

    public function format(int $amount): string {
        return TextFormat::AQUA . $this->getName() . TextFormat::GRAY . "x" . TextFormat::AQUA . $amount . TextFormat::GRAY;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getBuyPrice(int $amount = 1): ?int {
        if ($this->buyPrice === null) return null;
        return $this->buyPrice * $amount;
    }

    public function getSellPrice(int $amount = 1): ?int{
        if ($this->sellPrice === null) return null;
        return $this->sellPrice * $amount;
    }

    public function getButtonName(): string {
        return $this->buttonName;
    }

    public function getItem(): Item {
        return $this->item;
    }

    public function getCurrency(): string {
        return $this->currency;
    }

    public function canBeBought(): bool {
        return ($this->getBuyPrice() !== null);
    }

    public function canBeSold(): bool {
        return ($this->getSellPrice() !== null);
    }

    public function hasAmount(PlayerSession $session, int $cost): bool {
        $cost = intval($cost);
        $currency = $this->getCurrency();
        switch ($currency){
            case self::COINS:
                return $session->getMoneyModule()->getMoney() >= $cost;
            case self::OPALS:
                return $session->getMoneyModule()->getOpal() >= $cost;
            case self::XP:
                return $session->getPlayer()->getXpManager()->getCurrentTotalXp() >= $cost;
        }
        return false;
    }

    public function reduceAmount(PlayerSession $session, int $amount){
        $amount = intval($amount);
        switch ($this->getCurrency()){
            case self::COINS:
                $session->getMoneyModule()->removeMoney($amount);
                return;
            case self::OPALS:
                $session->getMoneyModule()->removeOpal($amount);
                return;
            case self::XP:
                $session->getPlayer()->getXpManager()->subtractXp($amount);
                return;
        }
    }

    public function addAmount(PlayerSession $session, int $amount){
        $amount = intval($amount);
        switch ($this->getCurrency()){
            case self::COINS:
                $session->getMoneyModule()->addMoney($amount);
                return;
            case self::OPALS:
                $session->getMoneyModule()->addOpal($amount);
                return;
            case self::XP:
                $session->getPlayer()->getXpManager()->addXp($amount);
                return;
        }
    }

}