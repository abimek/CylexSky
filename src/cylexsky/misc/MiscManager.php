<?php
declare(strict_types=1);


namespace cylexsky\misc;

use cylexsky\misc\economy\EconomyManager;
use cylexsky\misc\join\JoinHandler;
use cylexsky\misc\scoreboards\ScoreboardHandler;
use cylexsky\misc\shop\ShopHandler;

class MiscManager{

    private static $instance;

    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        self::$instance = $this;
        $this->initHandlers();
    }

    private function initHandlers(){
        new JoinHandler();
        new ScoreboardHandler();
        new EconomyManager();
        new ShopHandler();
    }

    public static function getInstance(){
        return self::$instance;
    }


    protected function close(): void
    {
        // TODO: Implement close() method.
    }
}