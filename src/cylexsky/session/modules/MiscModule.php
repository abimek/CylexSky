<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use core\main\text\TextFormat;
use core\ranks\types\RankTypes;
use cylexsky\session\PlayerSession;

class MiscModule{

    private $fly;
    private $session;
    private $islandChat = false;

    public function __construct(PlayerSession $session)
    {
        $this->session = $session;
    }

    public function tryFly(){

    }

    public function getSession(): PlayerSession{
        return $this->session;
    }

    public function inIslandChat(): bool {
        if($this->session->getIslandObject() === null){
            return false;
        }
        return $this->islandChat;
    }

    public function toggleIslandChat(){
        $this->getSession()->sendNotification("Toggled Island Chat!");
        $this->islandChat = !$this->islandChat;
    }

    public function toggleFly(){
        if ($this->fly){
            $this->fly = false;
            $this->session->getPlayer()->setAllowFlight(false);
            $this->session->sendGoodNotification("Flight Disabled!");
        }else{
            if ($this->canFly()){
                $this->enableFly();
            }
        }
    }

    public function inFlight(): bool {
        return $this->fly;
    }

    public function disableFly(){
        if ($this->session->getRank()->getType() === RankTypes::STAFF_RANK){
            return;
        }
        if ($this->fly === false){
            return;
        }
        $this->fly = true;
        if($this->session->getPlayer()->getAllowFlight()){ $this->session->getPlayer()->setAllowFlight(false);}
        if($this->session->getPlayer()->isFlying()){$this->session->getPlayer()->setFlying(false);}
        $this->session->sendNotification(TextFormat::RED . "Flight Disabled!");
    }

    public function enableFly(){
        if ($this->fly) return;
        $this->fly = true;
        if(!$this->session->getPlayer()->getAllowFlight()){ $this->session->getPlayer()->setAllowFlight(true);}
        if(!$this->session->getPlayer()->isFlying()){$this->session->getPlayer()->setFlying(true);}
        $this->session->sendGoodNotification("Flight Enabled!");
    }

    public function canFly(): bool {
        return $this->canFlyInRegion();
    }

    public function canFlyInRegion(): bool {
        if ($this->session->getRank()->getType() === RankTypes::STAFF_RANK){
            return true;
        }
        $flyDisabledWorlds = ["world", "pvp", "trade", "resourceWorld"];
        if (!in_array($this->session->getPlayer()->getWorld()->getDisplayName(), $flyDisabledWorlds)){
            return true;
        }
        return false;
    }

}