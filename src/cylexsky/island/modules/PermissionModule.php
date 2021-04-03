<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use cylexsky\session\PlayerSession;

class PermissionModule extends BaseModule{

    public const OFFICER_AVAILABLE_PERMISSIONS = [
        self::PERMISSION_INVITE => false,
        self::PERMISSION_KICK => false,
        self::PERMISSION_BREAK => true,
        self::PERMISSION_PLACE => true,
        self::PERMISSION_OPEN_CONTAINER => true,
        self::PERMISSION_ATTACKMOB => true
    ];

    public const MEMBER_AVAILABLE_PERMISSIONS = [
        self::PERMISSION_BREAK => true,
        self::PERMISSION_PLACE => true,
        self::PERMISSION_OPEN_CONTAINER => true,
        self::PERMISSION_ATTACKMOB => true
    ];

    public const GUEST_AVAILABLE_PERMISSIONS = [
        self::PERMISSION_BREAK => false,
        self::PERMISSION_PLACE => false,
        self::PERMISSION_OPEN_CONTAINER => false,
        self::PERMISSION_ATTACKMOB => false
    ];

    public const TRUSTED_AVAILABLE_PERMISSIONS = [
        self::PERMISSION_BREAK => false,
        self::PERMISSION_PLACE => false,
        self::PERMISSION_OPEN_CONTAINER => false,
        self::PERMISSION_ATTACKMOB => false
    ];

    public const POSSIBLE_PERMISSIONS = [
        "Invite Permission",
        "Kick Permission",
        "Break Permission",
        "Place Permission",
        "Open Container Permission",
        "Attack Mob Permission"
    ];

    public const PERMISSION_INVITE = 0;
    public const PERMISSION_KICK = 1;
    public const PERMISSION_BREAK = 2;
    public const PERMISSION_PLACE = 3;
    public const PERMISSION_OPEN_CONTAINER = 4;
    public const PERMISSION_ATTACKMOB = 5;

    private $officerPermissions = [];
    private $memberPermissions = [];
    private $guestPermissions = [];
    private $trustedPermissions = [];

    public function init(array $data)
    {
        $this->trustedPermissions = $data[3];
        $this->officerPermissions = $data[2];
        $this->memberPermissions = $data[1];
        $this->guestPermissions = $data[0];
    }

    public function getPermissions(int $rank): array {
        switch ($rank){
            case Members::OFFICER:
                return $this->officerPermissions;
                break;
            case Members::MEMBER:
                return $this->memberPermissions;
                break;
            case Members::GUEST:
                return $this->guestPermissions;
                break;
            default:
                return [];
        }
    }

    public function removeTrusted(string $xuid){
        if (isset($this->trustedPermissions)){
            unset($this->trustedPermissions[$xuid]);
        }
    }

    public function getOfficerPermissions(): array {return $this->officerPermissions;}
    public function getMemberPermissions(): array {return $this->memberPermissions;}
    public function getGuestPermissions(): array {return $this->guestPermissions;}

    public function setPermissions(int $rank, array $permissions): bool {
        $this->getIsland()->setHasBeenChanged();
        switch ($rank){
            case Members::OFFICER:
                 $this->officerPermissions = $permissions;
                break;
            case Members::MEMBER:
                 $this->memberPermissions = $permissions;
                break;
            case Members::GUEST:
                 $this->guestPermissions = $permissions;
                break;
            default:
                return false;
        }
        return true;
    }

    public function setTrustedPermission(string $xuid, array $permissions){
        $this->trustedPermissions[$xuid] = $permissions;
    }

    public function hasPermission(int $rank, int $permission): bool {
        switch ($rank){
            case Members::OFFICER:
                $groupPerm = $this->officerPermissions;
                break;
            case Members::MEMBER:
                $groupPerm = $this->memberPermissions;
                break;
            case Members::GUEST:
                $groupPerm = $this->guestPermissions;
                break;
            default:
                return false;
        }
        if (array_key_exists($permission, $groupPerm) === false){
            return false;
        }
        return $groupPerm[$permission];
    }

    public function trustedHasPermission(string $xuid, int $permission): bool {
        if (isset($this->trustedPermissions[$xuid])){
            return (isset($this->trustedPermissions[$xuid][$permission])) ? $this->trustedPermissions[$xuid][$permission] : false;
        }
        return false;
    }

    public function playerHasPermission(int $permission, PlayerSession $session): bool {
        if ($session->getXuid() === $this->getIsland()->getOwner()){
            return true;
        }
        if ($session->getIslandObject() !== null && $session->getIslandObject()->getMembersModule()->isCoOwnerUsername($session->getObject()->getUsername())){
            return true;
        }
        if (isset($this->trustedPermissions[$session->getXuid()])){
            return $this->trustedHasPermission($session->getXuid(), $permission);
        }
        if ($this->getIsland()->getMembersModule()->isOfficer($session->getXuid())){
            return $this->hasPermission(Members::OFFICER, $permission);
        }
        if($this->getIsland()->getMembersModule()->isMemberXUID($session->getXuid())){
            return $this->hasPermission(Members::MEMBER, $permission);
        }
        return $this->hasPermission(Members::GUEST, $permission);
    }

    public static function getBaseData(): array
    {
        return [self::GUEST_AVAILABLE_PERMISSIONS, self::MEMBER_AVAILABLE_PERMISSIONS, self::TRUSTED_AVAILABLE_PERMISSIONS, self::TRUSTED_AVAILABLE_PERMISSIONS];
    }

    public function save()
    {
        return $this->encodeJson([$this->guestPermissions, $this->memberPermissions, $this->officerPermissions, $this->trustedPermissions]);
    }
}