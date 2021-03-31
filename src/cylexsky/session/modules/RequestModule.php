<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use core\main\text\TextFormat;
use cylexsky\CylexSky;
use cylexsky\island\Island;
use cylexsky\session\PlayerSession;
use pocketmine\scheduler\ClosureTask;

class RequestModule{

    public const TPA_REQUEST_TIMER = 15;
    public const ISLAND_INVITE_TIMER = 15;

    public $islandInvites = [];
    public $tpaRequests = [];

    private $session;

    public function __construct(PlayerSession $session)
    {
        $this->session = $session;
    }

    public function getSession(): PlayerSession{
        return $this->session;
    }

    public function getIslandInvites(): array {
        return $this->islandInvites;
    }

    public function inviteToIsland(PlayerSession $session){
        $name = $session->getObject()->getUsername();
        var_dump($name . " << NAME");
        if (!isset($this->islandInvites[$name])){
            $this->getSession()->sendNotification("You've been invited to " . $session->getIslandObject()->getOwnerName() . "'s Island!");
            $this->islandInvites[$name] = $session->getIslandObject();
            CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function ()use($name): void {
                if ($this !== null){
                    $this->removeIslandRequest($name);
                }
            }), 20 * self::ISLAND_INVITE_TIMER);
        }
    }

    public function removeIslandRequest(string $name){
        if (isset($this->islandInvites[$name])){
            unset($this->islandInvites[$name]);
        }
    }

    public function isIslandRequested(string $name){
        return isset($this->islandInvites[$name]);
    }

    public function getIsland(string $name): ?Island{
        if (!$this->isIslandRequested($name))
            return null;
        return $this->islandInvites[$name];
    }

    public function tpaRequest(PlayerSession $session){
        $name = $session->getPlayer()->getName();
        $thisName = $this->getSession()->getPlayer()->getName();
        if (!isset($this->tpaRequests[$name])){
            $this->tpaRequests[$name] = $session;
            CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function ()use($name, $session, $thisName): void {
                $this->removeTpaRequest($name);
                if ($session->getPlayer() !== null){
                    $session->sendNotification($thisName . TextFormat::GRAY . " failed to accept your TpaRequest!");
                }
            }), 20 * self::TPA_REQUEST_TIMER);
        }
    }

    public function removeTpaRequest(string $name){
        if (isset($this->tpaRequests[$name])){
            unset($this->tpaRequests[$name]);
        }
    }

}