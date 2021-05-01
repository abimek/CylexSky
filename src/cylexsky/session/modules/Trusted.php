<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use cylexsky\island\IslandManager;

class Trusted extends BaseModule{

    private $trusted;
    private $trustedLimit;

    public function init(array $data)
    {
        $this->trustedLimit = $data[0];
        $this->trusted = $data[1];
    }

    public static function getBaseData(): array
    {
        return [3, []];
    }

    public function addTrustedIsland(string $id){
        $this->getSession()->setHasBeenChanged();
        $this->trusted[$id] = $id;
    }

    public function isTrustedIsland(string $id){
        return isset($this->trusted[$id]);
    }

    public function removeTrustedIsland(string $id){
        $this->getSession()->setHasBeenChanged();
        if (isset($this->trusted[$id]))
            unset($this->trusted[$id]);
    }

    public function isTrustedLimitReached(): bool {
        return (count($this->trusted) >= $this->trustedLimit);
    }

    public function getTrustedCount(): int {
        return count($this->trusted);
    }
    public function getTrustedIslands(): array {
        $islands = [];
        foreach ($this->trusted as $id){
            $is = IslandManager::getIsland($id);
            if ($is !== null){
                $islands[] = $is;
            }else{
                unset($this->trusted[$id]);
            }
        }
        return $islands;
    }

    public function save()
    {
        return $this->encodeJson([$this->trustedLimit, $this->trusted]);
    }
}