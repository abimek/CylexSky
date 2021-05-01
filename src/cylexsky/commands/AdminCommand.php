<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\text\TextFormat;
use cylexsky\custom\CustomManager;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\ui\player\PlayerUIHandler;
use cylexsky\utils\Glyphs;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;

class AdminCommand extends Command{

    public const NAME = "admin";
    public const DESCRIPTION = "admin";
    public const USAGE = TextFormat::RED . "/admin";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, null, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            return;
        }
        $s = SessionManager::getSession($sender->getXuid());
        if (!$s->isServerOperator()){
            return;
        }
        if (!isset($args[0])){
            return;
        }
        switch ($args[0]){
            case "shop":
                PlayerUIHandler::sendShopUI($s);
                return;
            case "bank":
                PlayerUIHandler::sendBankUI($s);
                return;
            case "custom":
                $this->sendCustomItemsInv($s);
                return;
        }
    }

    private function sendCustomItemsInv(PlayerSession $session, int $page = 0){
        $items = array_slice(CustomManager::getItems(), ($page * 26), ($page*26)+26);
        if (empty($items)){
            $session->sendAdminMessage("There are no items on that page");
            return;
        }
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $inv = $menu->getInventory();
        $leftPage = ItemFactory::getInstance()->get(ItemIds::PAPER);
        $leftPage->getNamedTag()->setString("leftPage", "ss");
        $rightPage = ItemFactory::getInstance()->get(ItemIds::PAPER);
        $rightPage->getNamedTag()->setString("rightPage", "ss");
        $menu->setName(TextFormat::BOLD_RED . Glyphs::SWORD_RIGHT . "Page: " . TextFormat::RESET_AQUA . "$page" . Glyphs::SWORD_LEFT);
        $p = $page-1;
        if ($page <= 0){
            $leftPage->setCustomName(Glyphs::LEFT_ARROW . TextFormat::RED . " no pages to the left");
            $leftPage->getNamedTag()->setString("leftPage", "nn");
        }else{
            $leftPage->setCustomName(Glyphs::LEFT_ARROW . TextFormat::GREEN . " go to left page $p");
        }
        $p = $page + 1;
        $e = empty(array_slice(CustomManager::getItems(), ($p * 26), ($p*26)+26));
        if ($e){
            $rightPage->setCustomName(TextFormat::RED . "No pages to the right " . Glyphs::RIGHT_ARROW);
            $rightPage->getNamedTag()->setString("rightPage", "nn");
        }else{
            $rightPage->setCustomName(TextFormat::GREEN . "Go to page $p" . Glyphs::RIGHT_ARROW);
        }
        $inv->setItem(18, $leftPage);
        $inv->setItem(26, $rightPage);
        foreach ($items as $item){
            $inv->addItem($item);
        }
        $menu->setListener(InvMenu::readonly());
        $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction)use($session, $page): void {
            $item = $transaction->getItemClicked();
            if ($item->getNamedTag()->hasTag("leftPage")){
                if ($item->getNamedTag()->getTagValue("leftPage", StringTag::class) === "ss"){
                    return;
                }
                $transaction->getAction()->getInventory()->onClose($transaction->getPlayer());
                $this->sendCustomItemsInv($session, $page-1);
                return;
            }
            if ($item->getNamedTag()->hasTag("rightPage")){
                if ( $item->getNamedTag()->getTagValue("rightPage", StringTag::class) === "ss"){
                    return;
                }
                $transaction->getAction()->getInventory()->onClose($transaction->getPlayer());
                $this->sendCustomItemsInv($session, $page+1);
                return;
            }
            $transaction->getPlayer()->getInventory()->addItem($item);
        }));
        $menu->send($session->getPlayer());
    }

}