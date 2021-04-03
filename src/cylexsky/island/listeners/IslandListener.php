<?php
declare(strict_types=1);

namespace cylexsky\island\listeners;

use core\main\text\TextFormat;
use cylexsky\island\IslandManager;
use cylexsky\island\modules\PermissionModule;
use cylexsky\island\modules\SettingsModule;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use cylexsky\worlds\WorldManager;
use pocketmine\block\tile\Container;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;

class IslandListener implements Listener{

    public function entityDamageEntity(EntityDamageByEntityEvent $event){
        if (in_array($event->getEntity()->getWorld()->getFolderName(), WorldManager::getWorldNames())){
            return;
        }
        $damager = $event->getDamager();
        $is = IslandManager::getIsland($event->getEntity()->getWorld()->getFolderName());
        if ($is === null){
            return;
        }
        if ($event->getEntity() instanceof Player){
            if ($is->getSettingsModule()->getSetting(SettingsModule::PVP) === false){
                $event->cancel();
                return;
            }
        }
        if ($damager instanceof Player){
            $session = SessionManager::getSession($damager->getXuid());
            if ($is->getPermissionModule()->playerHasPermission(PermissionModule::PERMISSION_ATTACKMOB, $session) === false){
                $event->cancel();
                return;
            }
        }
    }

    public function entityDamage(EntityDamageEvent $event){
        if (in_array($event->getEntity()->getWorld()->getFolderName(), WorldManager::getWorldNames())){
            return;
        }
        if ($event instanceof EntityDamageByEntityEvent){
            return;
        }
        $entity = $event->getEntity();
        if (!$entity instanceof Player){
            return;
        }
        $is = IslandManager::getIsland($entity->getWorld()->getFolderName());
        if ($is === null){
            return;
        }
        $session = SessionManager::getSession($entity->getXuid());
        if ($event->getCause() === EntityDamageEvent::CAUSE_VOID){
            $event->cancel();
            $session->getPlayer()->sendMessage(Glyphs::SKULL . TextFormat::GRAY . "Fell into the " . TextFormat::RED . "void");
            $is->teleportPlayer($entity);
            return;
        }
        if ($event->getCause() === EntityDamageEvent::CAUSE_DROWNING || $event->getCause() === EntityDamageEvent::CAUSE_FALL || $event->getCause() === EntityDamageEvent::CAUSE_LAVA || $event->getCause() === EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
            $event->cancel();
            return;
        }
    }

    public function blockBreak(BlockBreakEvent $event){
        if (in_array($event->getPlayer()->getWorld()->getFolderName(), WorldManager::getWorldNames())){
            return;
        }
        $player = $event->getPlayer();
        $is = IslandManager::getIsland($player->getWorld()->getFolderName());
        if ($is === null){
            return;
        }
        $session = SessionManager::getSession($player->getXuid());
        if ($is->getPermissionModule()->playerHasPermission(PermissionModule::PERMISSION_BREAK, $session) === false){
            $event->cancel();
            return;
        }
    }

    public function blockPlace(BlockPlaceEvent $event){
        if (in_array($event->getPlayer()->getWorld()->getFolderName(), WorldManager::getWorldNames())){
            return;
        }
        $player = $event->getPlayer();
        $is = IslandManager::getIsland($player->getWorld()->getFolderName());
        if ($is === null){
            return;
        }
        $session = SessionManager::getSession($player->getXuid());
        if ($is->getPermissionModule()->playerHasPermission(PermissionModule::PERMISSION_PLACE, $session) === false){
            $event->cancel();
            return;
        }
    }

    public function interactInventory(PlayerInteractEvent $event){
        if (in_array($event->getPlayer()->getWorld()->getFolderName(), WorldManager::getWorldNames())){
            return;
        }
        if (!$event->getPlayer()->getWorld()->getTile($event->getBlock()->getPos()) instanceof Container){
            return;
        }
        $player = $event->getPlayer();
        $is = IslandManager::getIsland($player->getWorld()->getFolderName());
        if ($is === null){
            return;
        }
        $session = SessionManager::getSession($player->getXuid());
        if ($is->getPermissionModule()->playerHasPermission(PermissionModule::PERMISSION_OPEN_CONTAINER, $session) === false){
            $session->sendNotification("You can't open that!");
            $event->cancel();
            return;
        }
    }

    public function explode(EntityExplodeEvent $event){
        if (in_array($event->getEntity()->getWorld()->getFolderName(), WorldManager::getWorldNames())){
            return;
        }
        $entity = $event->getEntity();
        $is = IslandManager::getIsland($entity->getWorld()->getFolderName());
        if ($is === null){
            return;
        }
        if ($is->getSettingsModule()->getSetting(SettingsModule::EXPLOSIONS) === false){
            $event->cancel();
            return;
        }
    }
}