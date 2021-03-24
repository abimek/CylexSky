<?php
declare(strict_types=1);

namespace cylexsky\misc\join;

use core\main\text\TextFormat;
use cylexsky\CylexSky;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use cylexsky\utils\Sounds;
use cylexsky\worlds\worlds\MainWorld;
use pocketmine\entity\animation\TotemUseAnimation;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\Server;

class JoinHandler{

    public function __construct()
    {
        Server::getInstance()->getPluginManager()->registerEvents(new JoinListener(), CylexSky::getInstance());
    }

    public static function onJoin(Player $player){
        MainWorld::teleport($player);
        self::sendJoinMessages($player);
        self::sendJoinSound($player);
    }

    public static function initialJoin(Player $player){
        MainWorld::teleport($player);
        self::sendTotemAnimation($player);
        $player->sendMessage(Glyphs::LEXY_LINE_1 . TextFormat::GRAY . "Welcome to " . TextFormat::BOLD_GOLD . "Cylex" . TextFormat::AQUA . "Sky!");
        $player->sendMessage(Glyphs::LEXY_LINE_2 . TextFormat::GRAY . "My name is" . TextFormat::AQUA . " Lexy! " . TextFormat::GRAY . " And I'm");
        $player->sendMessage(Glyphs::LEXY_LINE_3 . TextFormat::GRAY . "your guide through the server!");
    }

    public static function sendJoinMessages(Player $player){
        $session = SessionManager::getSession($player->getXuid());
        self::sendTotemAnimation($player);
        if ($session !== null && $session->getTogglesModule()->joinMessage()){
            $player->sendMessage(Glyphs::LEXY_LINE_1 . TextFormat::GRAY . "Welcome back " . TextFormat::BOLD_GOLD . "Cylex" . TextFormat::AQUA . "Sky!");
            $player->sendMessage(Glyphs::LEXY_LINE_2 . TextFormat::GRAY . "An " . TextFormat::RED . "exciting" . TextFormat::GRAY . " journey awaits!");
            $player->sendMessage(Glyphs::LEXY_LINE_3 . TextFormat::GRAY);
        }
    }

    public static function sendJoinSound(Player $player){
        $session = SessionManager::getSession($player->getXuid());
        if ($session !== null && $session->getTogglesModule()->joinMessage()){
            Sounds::sendSoundPlayer($player, Sounds::JOIN_SOUND);
        }
    }

    public static function sendTotemAnimation(Player $player){
        $item = $player->getInventory()->getItemInHand();
        $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(ItemIds::TOTEM));
        $player->broadcastAnimation(new TotemUseAnimation($player));
        $player->getInventory()->setItemInHand($item);
    }
}