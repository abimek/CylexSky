<?php
declare(strict_types=1);

namespace cylexsky\misc\join;

use core\main\text\TextFormat;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class JoinListener implements Listener{

    public function onJoin(PlayerJoinEvent $event){
        if ($event->getPlayer()->hasPlayedBefore()){
            JoinHandler::onJoin($event->getPlayer());
        }else{
            JoinHandler::initialJoin($event->getPlayer());
        }
        $s = SessionManager::getSession($event->getPlayer()->getXuid());
        if($s !== null){
            if ($s->isServerOperator()){
                $event->setJoinMessage("");
            }
        }
        $event->setJoinMessage(Glyphs::CROWN . TextFormat::GRAY . $event->getPlayer()->getName() . TextFormat::GOLD . " Joined!");
    }
}