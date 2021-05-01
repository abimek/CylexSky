<?php

namespace cylexsky\misc\economy\events;

use cylexsky\misc\economy\EconomyManager;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\event\Cancellable;


abstract class EconomyAPIEvent extends Event implements Cancellable{

    use CancellableTrait;

    /** @var string */
    private $issuer;

    public function __construct(EconomyManager $plugin, string $issuer){
        $this->issuer = $issuer;
    }

    public function getIssuer() : string{
        return $this->issuer;
    }
}