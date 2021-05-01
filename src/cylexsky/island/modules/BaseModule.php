<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use core\main\data\formatter\JsonFormatter;
use cylexsky\island\Island;

abstract class BaseModule implements IModule {
    use JsonFormatter;

    private $island;

    public function __construct(string $data, Island $session)
    {
        $this->island = $session;
        $this->init($this->decodeJson($data));
    }

    public function getIsland(): Island{
        return $this->island;
    }

    public function completeReset(){
        $this->init(self::getBaseData());
    }

    public function init(array $data){
    }

    public static function getBaseData(): array {
        return [];
    }

}