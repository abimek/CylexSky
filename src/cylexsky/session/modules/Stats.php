<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

class Stats extends BaseModule{

    private $kills;
    private $deaths;
    private $blocksBroken = [];

    public function init(array $data)
    {
        $this->kills = $data[0];
        $this->deaths = $data[1];
        $this->blocksBroken = $data[2];
    }

    public function getKills(): int {
        return $this->kills;
    }

    public function getDeaths(): int {
        return $this->deaths;
    }

    public function getBlocksBroken(int $id, int $meta = 0){
        $v = $id . "|" . $meta;
        if (isset($this->blocksBroken[$v])) return$this->blocksBroken[$v];
        return 0;
    }

    public function addBlockBroken(int $id, int $meta){
        $v = $id . "|" . $meta;
        if (!isset($this->blocksBroken[$v])){
            $this->blocksBroken[$v] = 1;
            return;
        }
        $this->blocksBroken[$v]++;
        return;
    }

    public function getKD(): float {
        return ($this->deaths == 0) ? (float)$this->kills : (float) $this->kills / (float)$this->deaths;
    }

    public function addKill(){
        $this->getSession()->setHasBeenChanged();
        $this->kills++;
    }

    public function addDeath(){
        $this->getSession()->setHasBeenChanged();
        $this->deaths++;
    }

    public static function getBaseData(): array
    {
        return [0, 0, []];
    }

    public function save()
    {
        return $this->encodeJson([$this->kills, $this->deaths, $this->blocksBroken]);
    }
}