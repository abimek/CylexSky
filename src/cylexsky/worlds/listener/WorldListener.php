<?php
declare(strict_types=1);

namespace cylexsky\worlds\listener;

use cylexsky\worlds\worlds\PvPWorld;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;

class WorldListener implements Listener{
    
    public function entityDamage(EntityDamageEvent $event){
        $world = $event->getEntity()->getWorld();
        if ($world->getFolderName() === "world" || !$event instanceof EntityDamageByEntityEvent){
            $event->cancel();
            return;
        }
        if ($world->getFolderName() === "pvp" && $event instanceof EntityDamageByEntityEvent && PvPWorld::isPositionInsideArena($event->getDamager()->getPosition())){
            $event->cancel();
        }
    }
    
    public function blockPlace(BlockPlaceEvent $event){
        $world = $event->getPlayer()->getWorld();
        if ($world->getFolderName() === "world" || $world->getFolderName() === "pvp"){
            $event->cancel();
        }
    }
    
    public function blockBreak(BlockBreakEvent $event){
        $world = $event->getPlayer()->getWorld();
        if ($world->getFolderName() === "world" || $world->getFolderName() === "pvp"){
            $event->cancel();
        }
    }

    public function entityExplode(EntityExplodeEvent $event){
        $world = $event->getEntity()->getWorld();
        if ($world->getFolderName() === "world" || $world->getFolderName() === "pvp"){
            $event->cancel();
        }
    }
    
    public function cancelIfSpawn(){
        
    }
    
    public function cancelIfPvP(){
        
    }
    
}