<?php
declare(strict_types=1);

namespace cylexsky\main\listener;

use cylexsky\main\RankHandler;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

class ChatListener implements Listener{

    public function chat(PlayerChatEvent $event){
        if ($event->isCancelled()){
            return;
        }
        RankHandler::$messages[$event->getPlayer()->getXuid()] = [$event->getMessage(), time()];
    }
}