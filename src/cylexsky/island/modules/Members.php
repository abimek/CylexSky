<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

class Members extends BaseModule{

    public const MAX_LIMIT = 25;

    public const MEMBER = 0;
    public const OFFICER = 1;

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

    public function addToMemberLimit(int $amount){
        if ($amount + $this->memberLimit > self::MAX_LIMIT){
            $this->memberLimit = self::MAX_LIMIT;
        }else{
            $this->memberLimit += abs($amount);
        }
    }

    public function isMemberXUID(string $xuid){
        return isset($this->members[$xuid]);
    }

    public function isMemberUsername(string $name){
        return isset($this->membersByName[$name]);
    }

    public function addMember(string $name, string $xuid): bool {
        if (count($this->members) >= $this->getMemberLimit()){
            return false;
        }
        $this->members[$xuid] = [$name, self::MEMBER];
        $this->membersByName[$xuid] = [$xuid, self::MEMBER];
        return true;
    }

    public function promote(string $xuid){
        if (isset($this->members[$xuid])){
            $this->members[$xuid][1] = self::OFFICER;
            $this->membersByName[$this->members[$xuid][0]][1] = self::OFFICER;
        }
    }

    public function demote(string $xuid){
        if (isset($this->members[$xuid])){
            $this->members[$xuid][1] = self::MEMBER;
            $this->membersByName[$this->members[$xuid][0]][1] = self::MEMBER;
        }
    }

    public function nameToXuid(string $name): ?string {
        if (isset($this->membersByName[$name])){
            return $this->membersByName[$name][0];
        }
        return null;
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