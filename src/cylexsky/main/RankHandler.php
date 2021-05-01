<?php
declare(strict_types=1);

namespace cylexsky\main;

use core\main\text\TextFormat;
use core\main\text\utils\TextTimeUtil;
use core\main\text\utils\TextUtil;
use core\ranks\RankManager;
use core\ranks\ranks\Rookie;
use core\ranks\types\RankTypes;
use core\ranks\types\StaffRankIdentifiers;
use cylexsky\CylexSky;
use cylexsky\main\listener\ChatListener;
use cylexsky\main\ranks\Archer;
use cylexsky\main\ranks\Elite;
use cylexsky\main\ranks\King;
use cylexsky\main\ranks\Knight;
use cylexsky\main\ranks\Lord;
use cylexsky\main\ranks\Master;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Server;

class RankHandler{

    public const PROMO_DELAYS = [
        15,
        12,
        10,
        8,
        6,
        4,
        2
    ];

    public static $messages = [];

    private static $promoDelay = [];

    public function __construct()
    {
        Server::getInstance()->getPluginManager()->registerEvents(new ChatListener(), CylexSky::getInstance());
        $this->registerRanks();
        $this->editRankChatFormats();
        $this->editRankDisplayTags();
        $this->registerChatEditCallable();
        $this->registerDisplayTagEditCallable();
    }

    private function registerRanks(){
        $ranks = [
            new Archer(),
            new Elite(),
            new King(),
            new Knight(),
            new Lord(),
            new Master()
        ];
        foreach ($ranks as $rank){
            RankManager::registerRank($rank);
        }
    }

    private function registerChatEditCallable(){
        $callable = function (string &$format, string $xuid, PlayerChatEvent $event){
            $session = SessionManager::getSession($xuid);
            if ($session === null){
                return;
            }
            $session->getEmojisModule()->replaceEmojis($format);
            $msg = TextUtil::replaceText($format, [" skfnegienig" => " skfnegienig"]);
            if ($session->getIslandObject() !== null){
                $msg = TextUtil::replaceText($msg, ["{islevel}" => strval($session->getIslandObject()->getLevelModule()->getLevel())]);
            }else{
                $msg = TextUtil::replaceText($msg, [TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" => ""]);
            }
            $msg = TextUtil::replaceText($msg, ["{name}" => $session->getPlayer()->getName()]);
            if ($session->getRank()->getType() === RankTypes::STAFF_RANK){
                $d = 60 * 2;
            }else{
                $d = 60 * self::PROMO_DELAYS[$session->getRank()->getLevel()-1];
            }
            if (strpos($msg, "[item]") !== false && isset(self::$promoDelay[$xuid]) && self::$promoDelay[$xuid]+$d > time()){
                $t = self::$promoDelay[$xuid]+$d - time();
                $session->sendNotification("You can send an " . TextFormat::BOLD_YELLOW . "AD " . TextFormat::RESET_GRAY . "in " . TextTimeUtil::secondsToTime($t, TextFormat::RED, TextFormat::GRAY));
                $event->cancel();
                return;
            }
            if (strpos($msg, "[item]") !== false && (!isset(self::$promoDelay[$xuid]) || self::$promoDelay[$xuid]+$d < time())){
                self::$promoDelay[$xuid] = time()+$d;
                if ($session->getPlayer()->getInventory()->getItemInHand()->getCustomName() === ""){
                    $msg = $this->str_replace_once($msg, "[item]", TextFormat::YELLOW . $session->getPlayer()->getInventory()->getItemInHand()->getName(). TextFormat::RESET_WHITE . "(" . TextFormat::GRAY . $session->getPlayer()->getInventory()->getItemInHand()->getCount() . "x" . TextFormat::WHITE . ")");
                }else{
                    $msg = $this->str_replace_once($msg, "[item]", TextFormat::YELLOW . $session->getPlayer()->getInventory()->getItemInHand()->getCustomName() . TextFormat::RESET_WHITE . "(" . TextFormat::GRAY . $session->getPlayer()->getInventory()->getItemInHand()->getCount() . "x" . TextFormat::WHITE . ")");
                }
                $msg = TextFormat::BOLD_YELLOW . "AD" . TextFormat::RESET . $msg;
            }
            RankHandler::$messages[$event->getPlayer()->getXuid()] = [$event->getMessage(), time()];
            $format = $msg;
            if ($session->getMiscModule()->inIslandChat()){
                $format = TextFormat::BOLD_GOLD . "Island" . TextFormat::GRAY . ">>" . TextFormat::RESET . $format;
                unset(self::$messages[$event->getPlayer()->getXuid()]);
                $people = array_merge($session->getIslandObject()->getMembersModule()->getOnlineMembers(), $session->getIslandObject()->getTrustedModule()->getOnlineTrusted());
                $event->setRecipients($people);
            }
            if ($event->isCancelled()){
                return;
            }
        };
        RankManager::registerEditCallable($callable);
    }

    private function str_replace_once($subject, $search, $replace) {
        $pos = strpos($subject, $search);
        if ($pos === false) {
            return $subject;
        }
        return substr($subject, 0, $pos) . $replace . substr($subject, $pos + strlen($search));
    }

    private function editRankChatFormats(){
        $owner = RankManager::getRank(StaffRankIdentifiers::OWNER_ID);
        $developer = RankManager::getRank(StaffRankIdentifiers::DEVELOPER_ID);
        $admin = RankManager::getRank(StaffRankIdentifiers::ADMIN_ID);
        $mod = RankManager::getRank(StaffRankIdentifiers::MOD_ID);
        $helper = RankManager::getRank(StaffRankIdentifiers::HELPER_ID);
        $rookie = RankManager::getRank(Rookie::ROOKIE);
            $owner->setChatFormat(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::RED . Glyphs::OWNER . " {name}" . TextFormat::DARK_GRAY . ": " . TextFormat::AQUA . "{msg}");
        $developer->setChatFormat(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::AQUA . Glyphs::OWNER . " {name}" . TextFormat::DARK_GRAY . ": " . TextFormat::DARK_AQUA . "{msg}");
        $admin->setChatFormat(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::DARK_RED . Glyphs::ADMIN . " {name}" . TextFormat::DARK_GRAY . ": " . TextFormat::DARK_AQUA . "{msg}");
        $mod->setChatFormat(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::BLUE . Glyphs::MOD . " {name}" . TextFormat::DARK_GRAY . ": " . TextFormat::DARK_AQUA . "{msg}");
        $helper->setChatFormat(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::YELLOW . Glyphs::HELPER . " {name}" . TextFormat::DARK_GRAY . ": " . TextFormat::DARK_AQUA . "{msg}");
        $rookie->setChatFormat(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::GRAY . Glyphs::ROOKIE . " {name}" . TextFormat::DARK_GRAY . ": " . TextFormat::GRAY . "{msg}");
    }

    private function editRankDisplayTags(){
        $owner = RankManager::getRank(StaffRankIdentifiers::OWNER_ID);
        $developer = RankManager::getRank(StaffRankIdentifiers::DEVELOPER_ID);
        $admin = RankManager::getRank(StaffRankIdentifiers::ADMIN_ID);
        $mod = RankManager::getRank(StaffRankIdentifiers::MOD_ID);
        $helper = RankManager::getRank(StaffRankIdentifiers::HELPER_ID);
        $rookie = RankManager::getRank(Rookie::ROOKIE);
        $owner->setDisplayTag(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::RED . Glyphs::OWNER . " {name}" .  TextFormat::AQUA . "{msg}");
        $developer->setDisplayTag(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::AQUA . Glyphs::OWNER . " {name}" .  TextFormat::DARK_AQUA . "{msg}");
        $admin->setDisplayTag(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::DARK_RED . Glyphs::ADMIN . " {name}" .  TextFormat::DARK_AQUA . "{msg}");
        $mod->setDisplayTag(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::BLUE . Glyphs::MOD . " {name}" . TextFormat::DARK_AQUA . "{msg}");
        $helper->setDisplayTag(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::YELLOW . Glyphs::HELPER . " {name}" . TextFormat::DARK_AQUA . "{msg}");
        $rookie->setDisplayTag(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::GRAY . Glyphs::ROOKIE . " {name}" . TextFormat::GRAY . "{msg}");
    }

    private function registerDisplayTagEditCallable(){
        $callable = function (&$display, string $xuid){
            $session = SessionManager::getSession($xuid);
            if ($session !== null){
                $msg = TextUtil::replaceText($display, [" skfnegienig" => " skfnegienig"]);
                if ($session->getIslandObject() !== null){
                    $msg = TextUtil::replaceText($msg, ["{islevel}" => strval($session->getIslandObject()->getLevelModule()->getLevel())]);
                }else{
                    $msg = TextUtil::replaceText($msg, [TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" => ""]);
                }
                $msg = TextUtil::replaceText($msg, ["{name}" => $session->getObject()->getUsername()]);
                if (isset(self::$messages[$xuid])){
                    $m = substr(self::$messages[$xuid][0], 0, 42);
                    if (self::$messages[$xuid][1]+5 <= time()){
                        unset(self::$messages[$xuid]);
                    }
                    $trueM = wordwrap($m, 21, "\n", true);
                    $session->getEmojisModule()->replaceEmojis($trueM);
                    $msg = TextUtil::replaceText($msg, ["{msg}" => "\n" . Glyphs::BUBBLE_MESSAGE . $trueM]);
                }else{
                    $msg = TextUtil::replaceText($msg, ["{msg}" => ""]);
                }
                $display = $msg;
            }
        };
        RankManager::registerDisplayTagCallabe($callable);
    }
}