<?php
declare(strict_types=1);
namespace cylexsky\ui\player;

use core\forms\formapi\Form;
use cylexsky\misc\shop\objects\Category;
use cylexsky\misc\shop\objects\ShopItem;
use cylexsky\session\PlayerSession;
use cylexsky\ui\player\uis\economy\bank\BankUI;
use cylexsky\ui\player\uis\economy\shop\CategoryItemsList;
use cylexsky\ui\player\uis\economy\shop\ShopCategories;
use cylexsky\ui\player\uis\economy\shop\ShopItemForm;
use cylexsky\ui\player\uis\misc\EmojiForm;
use cylexsky\ui\player\uis\toggles\TogglesUI;

class PlayerUIHandler{


    public static function sendUI(PlayerSession $session, Form $form){
        $session->getPlayer()->sendForm($form);
    }

    public static function sendBankUI(PlayerSession $session){
        self::sendUI($session, new BankUI($session));
    }

    public static function sendTogglesUI(PlayerSession $session){
        self::sendUI($session, new TogglesUI($session));
    }

    public static function sendShopUI(PlayerSession $session){
        self::sendUI($session, new ShopCategories($session));
    }

    public static function sendShopCategoryItemList(PlayerSession $session, Category $category){
        self::sendUI($session, new CategoryItemsList($session, $category));
    }

    public static function sendEmojiForm(PlayerSession $session){
        self::sendUI($session, new EmojiForm($session));
    }

    public static function sendShopItemFOrm(PlayerSession $session, ShopItem $shopItem){
        self::sendUI($session, new ShopItemForm($session, $shopItem));
    }
}