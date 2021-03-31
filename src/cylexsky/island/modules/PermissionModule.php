<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use cylexsky\session\PlayerSession;

class PermissionModule extends BaseModule{

    public const OFFICER_AVAILABLE_PERMISSIONS = [
        self::PERMISSION_INVITE,
        self::PERMISSION_KICK,
        self::PERMISSION_BREAK,
        self::PERMISSION_PLACE,
        self::PERMISSION_OPEN_CONTAINER,
        self::PERMISSION_ATTACKMOB
    ];

    public const MEMBER_AVAILABLE_PERMISSIONS = [
        self::PERMISSION_BREAK,
        self::PERMISSION_PLACE,
        self::PERMISSION_OPEN_CONTAINER,
        self::PERMISSION_ATTACKMOB
    ];

    public const GUEST_AVAILABLE_PERMISSIONS = [
        self::PERMISSION_BREAK,
        self::PERMISSION_PLACE,
        self::PERMISSION_OPEN_CONTAINER,
        self::PERMISSION_ATTACKMOB
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

    public function init(array $data)
    {
        $this->officerPermissions = $data[2];
        $this->memberPermissions = $data[1];
        $this->guestPermissions = $data[0];
    }

    public function setPermissions(int $rank, array $permissions): bool {
        $this->getIsland()->hasBeenChanged();
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

    public function hasPermission(int $rank, int $permission): bool {
        $groupPerm = [];
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
        return in_array($permission, $groupPerm);
    }

    public function playerHasPermission(int $permission, PlayerSession $session): bool {
        if ($session->getXuid() === $this->getIsland()->getOwner()){
            return true;
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
        return [[], [self::PERMISSION_OPEN_CONTAINER, self::PERMISSION_BREAK, self::PERMISSION_PLACE, self::PERMISSION_ATTACKMOB], [self::PERMISSION_OPEN_CONTAINER, self::PERMISSION_BREAK, self::PERMISSION_PLACE, self::PERMISSION_ATTACKMOB]];
    }

    public function save()
    {
        return $this->encodeJson([$this->guestPermissions, $this->memberPermissions, $this->officerPermissions]);
    }
}