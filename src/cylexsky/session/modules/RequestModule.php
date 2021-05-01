<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use core\main\text\TextFormat;
use cylexsky\CylexSky;
use cylexsky\island\Island;
use cylexsky\session\PlayerSession;
use pocketmine\scheduler\ClosureTask;

class RequestModule{

    public const TPA_REQUEST_TIMER = 30;
    public const ISLAND_INVITE_TIMER = 30;

    public $islandInvites = [];
    public $tpaRequests = [];
    private $trustedRequests = [];

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
        $name2 = $this->getSession()->getObject()->getUsername();
        if (!isset($this->islandInvites[$name])){
            $this->getSession()->sendNotification("You've been invited to " . $session->getIslandObject()->getOwnerName() . "'s Island by " . TextFormat::GOLD . $name . TextFormat::GRAY . "! Do " . TextFormat::RED . "/is accept (" . $name . ")" . TextFormat::GRAY . "to join the island!");
            $this->islandInvites[$name] = $session->getIslandObject();
            CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function ()use($name, $session, $name2): void {
                if (!$this->isIslandRequested($name)){
                    return;
                }
                if ($session->getPlayer() !== null){
                    $session->sendNotification("The island invite you sent to " . TextFormat::AQUA . $name2 . TextFormat::GRAY . " has expired!");
                }
                if ($this !== null){
                    if ($this->getSession()->getPlayer() !== null){
                        $this->getSession()->sendNotification("Island invitation from" . TextFormat::AQUA . $name .  TextFormat::GRAY . " has expired!");
                    }
                    $this->removeIslandRequest($name);
                }
            }), 20 * self::ISLAND_INVITE_TIMER);
        }
    }

    public function inviteToIslandAsTrusted(PlayerSession $session){
        $name = $session->getObject()->getUsername();
        $name2 = $this->getSession()->getObject()->getUsername();
        if (!isset($this->islandInvites[$name])){
            $this->getSession()->sendNotification("You've been invited to " . TextFormat::AQUA . $session->getIslandObject()->getOwnerName() . TextFormat::GRAY . "'s Island as trusted by " . TextFormat::GOLD . $name . TextFormat::GRAY . "! Do " . TextFormat::RED . "/is taccept (" . $name .")".TextFormat::GRAY . "to join the island!");
            $this->trustedRequests[$name] = $session->getIslandObject();
            CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function ()use($name, $session, $name2): void {
                if (!$this->isIslandTrustedRequested($name)){
                    return;
                }
                if ($session !== null){
                    $session->sendNotification("The island trusted invite you sent to " . TextFormat::AQUA . $name2 . TextFormat::GRAY . " has expired!");
                }
                if ($this !== null){
                    if ($this->getSession()->getPlayer() !== null){
                        $this->getSession()->sendNotification("Island " . TextFormat::GOLD . "trusted " . TextFormat::GRAY . "invitation from " . TextFormat::AQUA . $name .  TextFormat::GRAY . " has expired!");
                    }
                    $this->removeIslandTrustedRequest($name);
                }
            }), 20 * self::ISLAND_INVITE_TIMER);
        }
    }

    public function removeIslandRequest(string $name){
        if (isset($this->islandInvites[$name])){
            unset($this->islandInvites[$name]);
        }
    }

    public function removeIslandTrustedRequest(string $name){
        if (isset($this->trustedRequests[$name])){
            unset($this->trustedRequests[$name]);
        }
    }

    public function isIslandRequested(string $name){
        return isset($this->islandInvites[$name]);
    }

    public function isIslandTrustedRequested(string $name){
        return isset($this->trustedRequests[$name]);
    }

    public function getIsland(string $name): ?Island{
        if (!$this->isIslandRequested($name))
            return null;
        return $this->islandInvites[$name];
    }

    public function getTrustedInvite(string $name): ?Island{
        if (!$this->isIslandTrustedRequested($name)){
            return null;
        }
        return $this->trustedRequests[$name];
    }

    public function tpaRequest(PlayerSession $session){
        $name = $session->getPlayer()->getName();
        $thisName = $this->getSession()->getPlayer()->getName();
        if (!isset($this->tpaRequests[$name])){
            $this->tpaRequests[$name] = $session;
            $this->getSession()->sendGoodNotification("Teleport request from " . TextFormat::GOLD . $name . TextFormat::GREEN . "!" . TextFormat::GRAY . " Do /tpaaccept (" . $name . ") to accept the request!");
            CylexSky::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function ()use($name, $session, $thisName): void {
                if (!$this->isTpaRequest($name)){
                    return;
                }
                if ($this->getSession()->getPlayer() !== null){
                    $this->getSession()->sendGoodNotification("Tpa request from $name expired!");
                }
                $this->removeTpaRequest($name);
                if ($session->getPlayer() !== null){
                    $session->sendNotification(TextFormat::RED . $thisName . TextFormat::GRAY . " failed to accept your TpaRequest!");
                }
            }), 20 * self::TPA_REQUEST_TIMER);
        }
    }

    public function isTpaRequest(string $name): bool {
        return isset($this->tpaRequests[$name]);
    }

    public function removeTpaRequest(string $name){
        if (isset($this->tpaRequests[$name])){
            unset($this->tpaRequests[$name]);
        }
    }

}