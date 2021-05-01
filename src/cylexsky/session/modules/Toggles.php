<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

class Toggles extends BaseModule{

    public const TOGGLE_NAMES = [
        "Join Message",
        "Scoreboard",
        "Tpa Requests",
        "Spawn Sounds"
    ];

    public const JOIN_MESSAGE = 0;
    public const SCOREBOARD = 1;
    public const TPA_REQUESTS = 2;
    public const SPAWN_SOUND = 3;

    private $data;

    public function init(array $data)
    {
        $this->data = $data;
    }

    public function joinMessage(): bool{return $this->data[self::JOIN_MESSAGE];}
    public function toggleJoinMessage(): void {$this->data[self::JOIN_MESSAGE] = !$this->data[self::JOIN_MESSAGE];$this->getSession()->setHasBeenChanged();}
    public function scoreboards(): bool {return $this->data[self::SCOREBOARD];}
    public function toggleScoreboard(): void {$this->data[self::SCOREBOARD] = !$this->data[self::SCOREBOARD];$this->getSession()->setHasBeenChanged();}
    public function tpaRequests(): bool{return $this->data[self::TPA_REQUESTS];}
    public function toggleTpaRequests(): void {$this->data[self::TPA_REQUESTS] = !$this->data[self::TPA_REQUESTS];$this->getSession()->setHasBeenChanged();}
    public function spawnSounds(): bool {return $this->data[self::SPAWN_SOUND];}
    public function toggleSpawnSounds(): void {$this->data[self::SPAWN_SOUND] = !$this->data[self::SPAWN_SOUND];$this->getSession()->setHasBeenChanged();}

    public function toggle(int $id){
        $this->getSession()->setHasBeenChanged();
        if (isset($this->data[$id])){
            $this->data[$id] = !$this->data[$id];
        }
    }

    public function setToggles(array $data){
        $this->getSession()->setHasBeenChanged();
        $this->data = $data;
    }

    public function getToggles(): array {
        return $this->data;
    }

    public static function getBaseData(): array
    {
        return [
            self::JOIN_MESSAGE => true,
            self::SCOREBOARD => true,
            self::TPA_REQUESTS => true,
            self::SPAWN_SOUND => true
        ];
    }

    public function save()
    {
        return $this->encodeJson($this->data);
    }
}