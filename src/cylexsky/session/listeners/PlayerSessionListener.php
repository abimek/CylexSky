<?php
declare(strict_types=1);

namespace cylexsky\session\listeners;

use core\main\text\TextFormat;
use core\ranks\events\PlayerRankChangeEvent;
use core\ranks\Rank;
use cylexsky\session\database\PlayerSessionDatabaseHandler;
use cylexsky\session\modules\EmojiModule;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use cylexsky\utils\RankIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\network\mcpe\protocol\types\DeviceOS;

class PlayerSessionListener implements Listener{

    public static $deviceIds = [];

    public function login(PlayerLoginEvent $event){
        $id = $event->getPlayer()->getUniqueId()->toString();
        if (isset(self::$deviceIds[$id])){
            $v = self::$deviceIds[$id];
            unset(self::$deviceIds[$id]);
            self::$deviceIds[$event->getPlayer()->getXuid()] = $v;
        }
    }

    public function preLogin(PlayerPreLoginEvent $event){
        $os = $event->getPlayerInfo()->getExtraData()["DeviceOS"];
        if ($os === DeviceOS::WINDOWS_10){
            self::$deviceIds[$event->getPlayerInfo()->getUuid()->toString()] = PlayerSession::UI_INV;
        }else{
            self::$deviceIds[$event->getPlayerInfo()->getUuid()->toString()] = PlayerSession::UI_FORM;
        }
    }

    public function onBreak(BlockBreakEvent $event){
        $session = SessionManager::getSession($event->getPlayer()->getXuid());
        if (!$event->isCancelled()){
            $session->getStatsModule()->addBlockBroken($event->getBlock()->getId(), $event->getBlock()->getMeta());
        }
    }

    public function onDeath(PlayerDeathEvent $event){
        $session = SessionManager::getSession($event->getPlayer()->getXuid());
        $wealth = $session->getMoneyModule()->getMoney();
        $reduce = intval(floor($wealth * .2));
        $session->getMoneyModule()->removeMoney($reduce);
        $session->sendNotification("You've lost " . TextFormat::GOLD . $reduce . Glyphs::GOLD_COIN . TextFormat::GRAY . ", 20% wealth lost!");
    }

    public function deaths(PlayerDeathEvent $event){
        $session = SessionManager::getSession($event->getPlayer()->getXuid());
        $session->getStatsModule()->addDeath();
    }

    public function rankChange(PlayerRankChangeEvent $event){
        $rank = $event->getRank();
        assert($rank instanceof Rank);
        PlayerSessionDatabaseHandler::callableOfflineXuid($event->getPlayerObject()->getXuid(), function (PlayerSession $session)use($rank){
            switch ($rank->getLevel()){
                case RankIds::ARCHER:
                    $session->getEmojisModule()->addEmojis(EmojiModule::getArcherEmojis());
                    break;
                case RankIds::KNIGHT:
                    $session->getEmojisModule()->addEmojis(EmojiModule::getKnightEmojis());
                    break;
                case RankIds::LORD:
                    $session->getEmojisModule()->addEmojis(EmojiModule::getLordEmojis());
                    break;
                case RankIds::MASTER:
                    $session->getEmojisModule()->addEmojis(EmojiModule::getMasterEmojis());
                    break;
                case RankIds::ELITE:
                    $session->getEmojisModule()->addEmojis(EmojiModule::getEliteEmojis());
                    break;
                case RankIds::KING:
                    $session->getEmojisModule()->addEmojis(EmojiModule::getKingEmojis());
                    break;
            }
        });
    }
}