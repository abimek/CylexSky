<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

class Boosters extends BaseModule{

    private $xpBooster = 1;

    public function init(array $data)
    {
        $this->xpBooster = $data[0];
    }

    /**
     * @return int
     */
    public function getXpBooster(): int
    {
        return $this->xpBooster;
    }

    public static function getBaseData(): array
    {
        return [1];
    }

    public function save()
    {
        return $this->encodeJson([$this->xpBooster]);
    }
}