<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

class Toggles extends BaseModule{

    public const JOIN_MESSAGE = 0;
    public const SCOREBOARD = 1;

    private $data;

    public function init(array $data)
    {
        $this->data = $data;
    }

    public function joinMessage(): bool{return $this->data[self::JOIN_MESSAGE];}
    public function toggleJoinMessage(): void {$this->data[self::JOIN_MESSAGE] = !$this->data[self::JOIN_MESSAGE];}
    public function scoreboards(): bool {return $this->data[self::SCOREBOARD];}
    public function toggleScoreboard(): void {$this->data[self::SCOREBOARD] = !$this->data[self::SCOREBOARD];}

    public function toggle(int $id){
        if (isset($this->data[$id])){
            $this->data[$id] = !$this->data[$id];
        }
    }

    public static function getBaseData(): array
    {
        return [
            self::JOIN_MESSAGE => true,
            self::SCOREBOARD => true
        ];
    }

    public function save()
    {
        return $this->encodeJson($this->data);
    }
}