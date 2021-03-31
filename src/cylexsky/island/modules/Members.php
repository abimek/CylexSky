<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use core\database\objects\Query;
use core\main\text\TextFormat;
use cylexsky\session\database\PlayerSessionDatabaseHandler;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use pocketmine\Server;

class Members extends BaseModule{

    public const MAX_LIMIT = 25;

    public const GUEST = 0;
    public const MEMBER = 1;
    public const OFFICER = 2;
    public const OWNER = 3;

    private $memberLimit = 3;

    private $members = [];
    private $membersByName = [];

    public function init(array $data)
    {
        $this->memberLimit = $data[0];
        $this->members = $data[1];
        foreach ($this->members as $member => $data){
            $this->membersByName[$data[0]] = [$member, $data[1]];
        }
    }

    public function getMemberLimit(): int {
        return $this->memberLimit;
    }

    public function getMembers(): array {
        return $this->members;
    }

    public function getMemberXuids(): array {
        return array_keys($this->members);
    }

    public function addToMemberLimit(int $amount){
        $this->getIsland()->hasBeenChanged();
        if ($amount + $this->memberLimit > self::MAX_LIMIT){
            $this->memberLimit = self::MAX_LIMIT;
        }else{
            $this->memberLimit += abs($amount);
        }
    }

    public function getMemberCount(): int {
        return count($this->members);
    }

    public function isMemberXUID(string $xuid){
        return isset($this->members[$xuid]);
    }

    public function isOfficer(string $xuid): bool {
        if ($this->isMemberXUID($xuid) && $this->members[$xuid][1] === self::OFFICER){
            return true;
        }
        return false;
    }

    public function isOfficerUsername(string $username): bool {
        if ($this->isMemberUsername($username) && $this->membersByName[$username][1] === self::OFFICER){
            return true;
        }
        return false;
    }

    public function isTrueMember(string $xuid): bool {
        if ($this->isMemberXUID($xuid) && $this->members[$xuid][1] === self::MEMBER){
            return true;
        }
        return false;
    }

    public function isTrueMemberUsername(string $username): bool {
        if ($this->isMemberUsername($username) && $this->membersByName[$username][1] === self::MEMBER){
            return true;
        }
        return false;
    }

    public function isMemberLimitReached(): bool {
        return (count($this->members) >= $this->getMemberLimit());
    }

    public function isMemberUsername(string $name){
        return isset($this->membersByName[$name]);
    }

    public function addMember(PlayerSession $session, string $name, string $xuid): bool {
        $this->getIsland()->hasBeenChanged();
        if (count($this->members) >= $this->getMemberLimit()){
            $session->sendNotification("Island is full!");
            return false;
        }
        $session->sendGoodNotification("Successfully joined island!");
        $session->setIsland($this->getIsland()->getId());
        $this->members[$xuid] = [$name, self::MEMBER];
        $this->membersByName[$name] = [$xuid, self::MEMBER];
        foreach ($this->getOnlineMembers() as $member){
            $session = SessionManager::getSession($member->getXuid());
            $session->sendGoodNotification("$name " . TextFormat::GRAY . "joined the island!");
        }
        return true;
    }

    public function promote(string $xuid){
        $this->getIsland()->hasBeenChanged();
        if (isset($this->members[$xuid])){
            $this->members[$xuid][1] = self::OFFICER;
            $this->membersByName[$this->members[$xuid][0]][1] = self::OFFICER;
            foreach ($this->getOnlineMembers() as $member){
                $session = SessionManager::getSession($member->getXuid());
                $session->sendGoodNotification($this->members[$xuid][0] . " " . TextFormat::GRAY . "was promoted to officer!");
            }
        }
    }

    public function promoteName(string $name){
        $this->getIsland()->hasBeenChanged();
        if (isset($this->membersByName[$name])){
            $this->membersByName[$name][1] = self::OFFICER;
            $this->members[$this->membersByName[$name][0]][1] = self::OFFICER;
            foreach ($this->getOnlineMembers() as $member){
                $session = SessionManager::getSession($member->getXuid());
                $session->sendGoodNotification($name . " " . TextFormat::GRAY . "was promoted to officer!");
            }
        }
    }

    public function demote(string $xuid){
        $this->getIsland()->hasBeenChanged();
        if (isset($this->members[$xuid])){
            $this->members[$xuid][1] = self::MEMBER;
            $this->membersByName[$this->members[$xuid][0]][1] = self::MEMBER;
            foreach ($this->getOnlineMembers() as $member){
                $session = SessionManager::getSession($member->getXuid());
                $session->sendNotification($this->members[$xuid][0] . " " . TextFormat::GRAY . "was demoted to member!");
            }
        }
    }

    public function kick(string $name, bool $left = false){
        $this->getIsland()->hasBeenChanged();
        if ($this->isMemberUsername($name)){
            $xuid = $this->nameToXuid($name);
            unset($this->membersByName[$name]);
            unset($this->members[$xuid]);
            $s = SessionManager::getSession($xuid);
            if ($s !== null){
                $s->setIsland(null);
                if (!$left){
                    $s->sendNotification("You were kicked from " . TextFormat::GOLD . $this->getIsland()->getOwnerName() . "'s " . TextFormat::RED . "island!");
                }else{
                    $s->sendGoodNotification("Successfully left " . TextFormat::GOLD . $this->getIsland()->getOwnerName() . "'s " . TextFormat::GREEN . "island!");
                }
            }else{
                PlayerSessionDatabaseHandler::callableOfflineXuid($xuid, function (PlayerSession $session){
                    $session->setIsland(null);
                });
            }
            foreach ($this->getOnlineMembers() as $member){
                $session = SessionManager::getSession($member->getXuid());
                if ($left){
                    $s->sendGoodNotification($name . " " . TextFormat::GRAY . "successfully left the island!");
                }else {
                    $session->sendNotification($name . " " . TextFormat::GRAY . "was kicked from the island!");
                }
            }
        }
    }

    public function demoteName(string $name){
        $this->getIsland()->hasBeenChanged();
        if (isset($this->membersByName[$name])){
            $this->membersByName[$name][1] = self::MEMBER;
            $this->members[$this->membersByName[$name][0]][1] = self::MEMBER;
            foreach ($this->getOnlineMembers() as $member){
                $session = SessionManager::getSession($member->getXuid());
                $session->sendNotification($name . " " . TextFormat::GRAY . "was demoted to member!");
            }
        }
    }

    public function nameToXuid(string $name): ?string {
        if (isset($this->membersByName[$name])){
            return $this->membersByName[$name][0];
        }
        return null;
    }

    public function getRank(string $name): ?int {
        if ($this->getIsland()->getOwnerName() === $name){
            return self::OWNER;
        }
        if ($this->isMemberUsername($name)){
            return $this->membersByName[$name][1];
        }
        return null;
    }

    public function getOnlineMembers(): array {
        $players = [];
        if (Server::getInstance()->getPlayerExact($this->getIsland()->getOwnerName()) !== null){
            $players[] = Server::getInstance()->getPlayerExact($this->getIsland()->getOwnerName());
        }
        foreach ($this->membersByName as $ic => $idc){
            if (Server::getInstance()->getPlayerExact($ic) !== null){
                $players[] = Server::getInstance()->getPlayerExact($ic);
            }
        }
        return $players;
    }

    public function getOfflineMembers(): array {
        $players = [];
        if (Server::getInstance()->getPlayerExact($this->getIsland()->getOwnerName()) === null){
            $players[] = $this->getIsland()->getOwner();
        }
        foreach ($this->membersByName as $ic => $idc){
            if (Server::getInstance()->getPlayerExact($ic) === null){
                $players[] = $idc[0];
            }
        }
        return $players;
    }


    public static function getBaseData(): array
    {
        return [3, []];
    }

    public function save()
    {
        return $this->encodeJson([$this->memberLimit, $this->members]);
    }
}