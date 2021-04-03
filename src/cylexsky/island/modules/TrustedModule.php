<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use core\main\text\TextFormat;
use cylexsky\session\database\PlayerSessionDatabaseHandler;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\worlds\worlds\MainWorld;
use pocketmine\Server;

class TrustedModule extends BaseModule{

    public const MAX_LIMIT = 10;

    private $trustedPeople;
    private $trustedLimit;

    public function init(array $data)
    {
        $this->trustedLimit = $data[0];
        $this->trustedPeople = $data[1];
    }

    public static function getBaseData(): array
    {
        return [3, []];
    }

    public function getTrustedLimit(): int {
        return $this->trustedLimit;
    }

    public function getTrustedXuids(): array {
        return array_keys($this->trustedPeople);
    }

    public function getTrustedNames(): array {
        return array_values($this->trustedPeople);
    }

    public function isTrustedLimitReached(): bool {
        return (count($this->trustedPeople) >= $this->trustedLimit);
    }

    public function addToTrusted(int $amount){
        $this->getIsland()->setHasBeenChanged();
        $this->getIsland()->hasBeenChanged();
        if ($amount + $this->trustedLimit > self::MAX_LIMIT){
            $this->trustedLimit = self::MAX_LIMIT;
        }else{
            $this->trustedLimit += abs($amount);
        }
    }

    public function isTrusted(string $xuid): bool {
        return isset($this->trustedPeople[$xuid]);
    }

    public function nameToXuid(string $name){
        return array_flip($this->trustedPeople)[$name];
    }

    public function isTrustedName(string $name): bool {
        return isset(array_flip($this->trustedPeople)[$name]);
    }

    public function addTrusted(PlayerSession $session, string $name, string $xuid): bool {
        $this->getIsland()->setHasBeenChanged();
        $this->getIsland()->hasBeenChanged();
        if (count($this->trustedPeople) >= $this->getTrustedLimit()){
            $session->sendNotification(TextFormat::RED  . "Island trusted slots are full!");
            return false;
        }
        $session->sendGoodNotification("Successfully joined island as a trusted!");
        $session->getTrustedModule()->addTrustedIsland($this->getIsland()->getId());
        $this->trustedPeople[$xuid] = $name;
        foreach ($this->getIsland()->getMembersModule()->getOnlineMembers() as $member){
            $session = SessionManager::getSession($member->getXuid());
            $session->sendGoodNotification("$name " . TextFormat::GRAY . "joined the island as " . TextFormat::GOLD . "trusted!");
        }
        return true;
    }

    public function getTrustedPeople(): array {
        return $this->trustedPeople;
    }

    public function getOnlineTrusted(): array {
        $players = [];
        foreach ($this->trustedPeople as $ic => $name){
            if (Server::getInstance()->getPlayerExact($name) !== null){
                $players[] = Server::getInstance()->getPlayerExact($name);
            }
        }
        return $players;
    }

    public function getTrustedCount(): int {
        return count($this->trustedPeople);
    }


    public function getOfflineTrustedXuids(): array {
        $players = [];
        foreach ($this->trustedPeople as $ic => $name){
            if (Server::getInstance()->getPlayerExact($name) === null){
                $players[] = $ic;
            }
        }
        return $players;
    }

    public function kick(string $name, bool $left = false){
        $this->getIsland()->setHasBeenChanged();
        if ($this->isTrustedName($name)){
            $xuid = $this->nameToXuid($name);
            unset($this->trustedPeople[$xuid]);
            $xuid = strval($xuid);
            $this->getIsland()->getPermissionModule()->removeTrusted($xuid);
            $s = SessionManager::getSession($xuid);
            if ($s !== null){
                $s->getTrustedModule()->removeTrustedIsland($this->getIsland()->getId());
                if($s->getPlayer()->getWorld()->getFolderName() === $this->getIsland()->getId()){
                    MainWorld::teleport($s->getPlayer());
                }
                if (!$left){
                    $s->sendNotification("You were kicked from " . TextFormat::GOLD . $this->getIsland()->getOwnerName() . "'s " . TextFormat::GRAY . "island!");
                }else{
                    $s->sendGoodNotification("Successfully left " . TextFormat::GOLD . $this->getIsland()->getOwnerName() . "'s " . TextFormat::GREEN . "island!");
                }
            }else{
                PlayerSessionDatabaseHandler::callableOfflineXuid($xuid, function (PlayerSession $session){
                    $session->getTrustedModule()->removeTrustedIsland($this->getIsland()->getId());
                });
            }
            foreach ($this->getIsland()->getMembersModule()->getOnlineMembers() as $member){
                $session = SessionManager::getSession($member->getXuid());
                if ($left){
                    $s->sendGoodNotification($name . " " . TextFormat::GRAY . "successfully left the island!");
                }else {
                    $session->sendNotification(TextFormat::RED . $name . " " . TextFormat::GRAY . "was kicked from the island!");
                }
            }
        }
    }

    public function save()
    {
        return $this->encodeJson([$this->trustedLimit, $this->trustedPeople]);
    }
}