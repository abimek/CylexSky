<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

class Stats extends BaseModule{

    private $kills;
    private $deaths;

    public function init(array $data)
    {
        $this->kills = $data[0];
        $this->deaths = $data[1];
    }

    public function getKills(): int {
        return $this->kills;
    }

    public function getDeaths(): int {
        return $this->deaths;
    }

    public function getKD(): float {
        return ($this->deaths == 0) ? (float)$this->kills : (float) $this->kills / (float)$this->deaths;
    }

    public static function getBaseData(): array
    {
        return [0, 0];
    }

    public function save()
    {
        return $this->encodeJson([$this->kills, $this->deaths]);
    }
}