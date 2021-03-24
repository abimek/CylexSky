<?php
declare(strict_types=1);


namespace cylexsky\misc;

use core\main\managers\Manager;
use cylexsky\misc\join\JoinHandler;
use cylexsky\misc\scoreboards\ScoreboardHandler;

class MiscManager extends Manager{

    private static $instance;

    protected function init(): void
    {
        self::$instance = $this;
        $this->initHandlers();
    }

    private function initHandlers(){
        new JoinHandler();
        new ScoreboardHandler();
    }

    public static function getInstance(){
        return self::$instance;
    }


    protected function close(): void
    {
        // TODO: Implement close() method.
    }
}