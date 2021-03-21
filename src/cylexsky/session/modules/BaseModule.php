<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use core\main\data\formatter\JsonFormatter;
use cylexsky\session\PlayerSession;

abstract class BaseModule implements IModule {
    use JsonFormatter;

    private $session;

    public function __construct(string $data, PlayerSession $session)
    {
        $this->session = $session;
        $this->init($this->decodeJson($data));
    }

    public function getSession(): PlayerSession{
        return $this->session;
    }

    public function init(array $data){

    }

    public static function getBaseData(): array {
        return [];
    }

}