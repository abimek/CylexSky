<?php
declare(strict_types=1);

namespace cylexsky\island\listeners;

use cylexsky\island\entities\Henry;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;

class JerryListener implements Listener{
    public function onDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
        if ($entity instanceof Henry){
            $event->cancel();
            return;
        }
    }
}