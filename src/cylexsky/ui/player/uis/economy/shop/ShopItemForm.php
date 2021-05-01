<?php
declare(strict_types=1);

namespace cylexsky\ui\player\uis\economy\shop;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\misc\shop\objects\ShopItem;
use cylexsky\session\PlayerSession;
use pocketmine\player\Player;

class ShopItemForm extends CustomForm{

    private $session;
    private $shopItem;

    public function __construct(PlayerSession $session, ShopItem $shopItem)
    {
        $this->session = $session;
        $this->shopItem = $shopItem;
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GOLD . $shopItem->getName());
        $content = "";
        $content .= TextFormat::BOLD_AQUA . "[" . TextFormat::RESET_RED . "Item" . TextFormat::BOLD_AQUA . "]" . TextFormat::RESET_GRAY . " " . $shopItem->getName() . "\n";
        $content .= TextFormat::BOLD_AQUA . "[" . TextFormat::RESET_RED . "Count" . TextFormat::BOLD_AQUA . "]" . TextFormat::RESET_GRAY . " " . $shopItem->getName() . "\n\n";
        $content .= TextFormat::BOLD_GRAY . "[" . TextFormat::RESET_RED . "Currency" . TextFormat::BOLD_GRAY . "]" . TextFormat::RESET_GRAY . " " .$shopItem->getCurrency() . "\n";
        if ($shopItem->getBuyPrice() !== null){
            $content .= TextFormat::BOLD_GRAY . "[" . TextFormat::RESET_RED . "Buy Price" . TextFormat::BOLD_GRAY . "]" . TextFormat::RESET_GRAY . $shopItem->getBuyPrice() . "\n";
        }else{
            $content .= TextFormat::BOLD_GRAY . "[" . TextFormat::RESET_RED . "Buy Price" . TextFormat::BOLD_GRAY . "]" . TextFormat::RESET_RED . "item can not be bought" . "\n";
        }
        if ($shopItem->getSellPrice() !== null){
            $content .= TextFormat::BOLD_GRAY . "[" . TextFormat::RESET_RED . "Sell Price" . TextFormat::BOLD_GRAY . "]" . TextFormat::RESET_GRAY . $shopItem->getSellPrice() . "\n\n";
        }else{
            $content .= TextFormat::BOLD_GRAY . "[" . TextFormat::RESET_RED . "Sell Price" . TextFormat::BOLD_GRAY . "]" . TextFormat::RESET_RED . "item can not be sold" . "\n\n";
        }
        $this->addLabel($content);
        if ($shopItem->canBeSold()){
            $this->addToggle(TextFormat::GOLD . "Sell", false);
        }
        if ($shopItem->canBeBought()){
            $this->addToggle(TextFormat::GOLD . "Buy", false);
        }
        $this->addSlider(TextFormat::GREEN . "Amount: ", 1, 500, 1, 1);
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data=null){
            if ($data === null){
                return;
            }
            $value = "sell";
            $amount = 1;
            if ($this->shopItem->canBeSold() && $this->shopItem->canBeBought()){
                $amount = $data[3];
                if ($data[1] && $data[2]){
                    $this->session->sendShopMessage("You can't purchase and sell at the same time!");
                    return;
                }
                if (!$data[1] && !$data[2]){
                    $this->session->sendShopMessage("You need to select where to buy or sell!");
                    return;
                }
                if ($data[1]){
                    $value = "sell";
                }else{
                    $value = "buy";
                }
            }else{
                $amount = $data[2];
                if ($data[1] === false){
                    $this->session->sendShopMessage("You didn't turn on the toggle!");
                    return;
                }
                if ($this->shopItem->canBeSold()){
                    $value = "sell";
                }else{
                    $value = "buy";
                }
            }
            $amount = intval($amount);
            switch ($value){
                case "sell":
                    $this->shopItem->sellItems($amount, $this->session->getPlayer());
                    return;
                case "buy":
                    $this->shopItem->buyItems($amount, $this->session->getPlayer());
                    return;
            }
        };
    }
}