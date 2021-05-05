<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\text\TextFormat;
use cylexsky\misc\economy\EconomyManager;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class PayCommand extends Command{

    public const NAME = "pay";
    public const DESCRIPTION = "pay gold coins to a player";
    public const USAGE = TextFormat::RED . "/pay <name> <amount>";

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
        if (!isset($args[1])){
            $s->sendNotification(self::USAGE);
            return;
        }
        if (!is_numeric($args[1])){
            $s->sendNotification(TextFormat::RED . $args[1] . TextFormat::GRAY . " isn't numeric!");
            return;
        }
        if (intval($args[1]) > $s->getMoneyModule()->getMoney()){
            $s->sendNotification("You dont have " . $args[1] . Glyphs::GOLD_COIN);
            return;
        }
        $player = $args[0];
        if(($p = Server::getInstance()->getPlayerByPrefix($args[0])) instanceof Player){
            $player = $p->getName();
        }
        if(!$p instanceof Player and EconomyManager::getInstance()->getConfig()->get("allow-pay-offline", true) === false){
            $s->sendNotification("Unable to send money to $player, either offline or doesnt exist!");
            return;
        }
        if(!EconomyManager::getInstance()->accountExists($player)){
            $s->sendNotification("That play doesnt exist!");
            return;
        }
        if ($p instanceof Player){
            $s->getMoneyModule()->removeMoney(intval($args[1]));
            $session = SessionManager::getSession($p->getXuid());
            $session->getMoneyModule()->addMoney(intval($args[1]));
            $s->sendNotification("Payed " . intval($args[1]) . Glyphs::GOLD_COIN . " to " .  TextFormat::GOLD . $args[0]);
            $session->sendGoodNotification(TextFormat::GOLD . $sender->getName() . TextFormat::GRAY . " payed you " . TextFormat::GOLD . $args[1] . Glyphs::OPAL);
            return;
        }
        EconomyManager::getInstance()->addMoney($player, intval($args[1]));
        $s->getMoneyModule()->removeMoney(intval($args[1]),true);
        $s->sendNotification("Payed " . intval($args[1]) . Glyphs::GOLD_COIN . " to " .  TextFormat::GOLD . $player);
    }

}