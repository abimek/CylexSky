<?php
declare(strict_types=1);

namespace cylexsky\custom\blocks\behavior\tasks;

use cylexsky\custom\blocks\behavior\StairChair;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class SendTask extends Task{

    private $who;
    private $data;
    private $instance;

    public function __construct(Player $player,array $data, StairChair $instance){
        $this->who = $player;
        $this->data = $data;
        $this->instance = $instance;
    }

    public function onRun() : void{
        foreach($this->data as $name => $datum){
            if(($player = Server::getInstance()->getPlayerExact($name)) === null || $this->who === null) continue;
            $this->instance->setSitting($player, $datum[1], $datum[0], $datum[2], $this->who);
        }
    }
}