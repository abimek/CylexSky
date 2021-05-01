<?php
namespace cylexsky\custom\blocks\behavior\listener;

use cylexsky\custom\blocks\behavior\StairChair;
use cylexsky\custom\blocks\behavior\StairSeat;
use cylexsky\custom\blocks\behavior\tasks\SendTask;
use cylexsky\custom\blocks\blocks\Chair;
use cylexsky\CylexSky;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InteractPacket;

class ChairListener implements Listener{
    private $instance;

    public function __construct(StairChair $instance){
        $this->instance = $instance;
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if($this->instance->isSitting($player)){
            $this->instance->unsetSitting($player);
        }
    }

    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (!$block instanceof Chair){
            return;
        }
        if(!$this->instance->isSitting($player) && $this->instance->canSit($player, $block)){
            if($usePlayer = $this->instance->isUsingSeat($block->getPos()->floor())){
                $player->sendMessage(str_replace(['@p','@b'],[$usePlayer->getName(), $block->getName()],$this->instance->config->get('tryto-sit-already-inuse')));
            }else{
                $eid = Entity::nextRuntimeId();
                $this->instance->setSitting($player, $block->getPos(),$block,  $eid);
                $player->sendTip(str_replace('@b',$block->getName(),$this->instance->config->get('send-tip-when-sit')));
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        //Can't apply without delaying that's why using delayed task
        if(count($this->instance->sit) >= 1) CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new SendTask($player, $this->instance->sit, $this->instance), 30);
    }

    public function onBreak(BlockBreakEvent $event){
        $block = $event->getBlock();
        if($this->instance->isStairBlock($block) && ($usingPlayer = $this->instance->isUsingSeat($block->getPos()->floor()))){
            $this->instance->unsetSitting($usingPlayer);
        }
    }

    public function onLeave(DataPacketReceiveEvent $event){
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();
        if($packet instanceof InteractPacket && $this->instance->isSitting($player) && $packet->action === InteractPacket::ACTION_LEAVE_VEHICLE){
            $this->instance->unsetSitting($player);
        }
    }
}