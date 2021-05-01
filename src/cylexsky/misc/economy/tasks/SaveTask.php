<?php
declare(strict_types=1);

namespace cylexsky\misc\economy\tasks;

use cylexsky\misc\economy\EconomyManager;
use pocketmine\scheduler\Task;

class SaveTask extends Task {

    protected $owner;

    public function __construct(EconomyManager $owner) {
        $this->owner = $owner;
    }

    public function onRun(): void {
        $this->owner->saveAll();
    }
}