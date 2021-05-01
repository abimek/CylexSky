<?php
declare(strict_types=1);

namespace cylexsky\misc\economy\events\money;

use cylexsky\misc\economy\EconomyManager;
use cylexsky\misc\economy\events\EconomyAPIEvent;

class SetMoneyEvent extends EconomyAPIEvent{

    public static $handlerList;

    /** @var string */
    private $username;

    /** @var float */
    private $amount;

    public function __construct(EconomyManager $plugin, string $username, float $amount, string $issuer){
        parent::__construct($plugin, $issuer);
        $this->username = $username;
        $this->amount = $amount;
    }

    public function getUsername() : string{
        return $this->username;
    }

    public function getAmount() : float{
        return $this->amount;
    }
}