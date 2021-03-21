<?php
declare(strict_types=1);

namespace cylexsky\misc\join;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class JoinListener implements Listener{

    public function onJoin(PlayerJoinEvent $event){
        if ($event->getPlayer()->hasPlayedBefore()){
            JoinHandler::onJoin($event->getPlayer());
        }else{
            JoinHandler::initialJoin($event->getPlayer());
        }
    }
}