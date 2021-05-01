<?php
declare(strict_types=1);

namespace cylexsky\misc\economy\events\money;

use cylexsky\misc\economy\EconomyManager;
use cylexsky\misc\economy\events\EconomyAPIEvent;

class MoneyChangedEvent extends EconomyAPIEvent{

    public static $handlerList;

    /** @var string */
    private $username;

    /** @var float */
    private $money;

    public function __construct(EconomyManager $plugin, string $username, float $money, string $issuer){
        parent::__construct($plugin, $issuer);
        $this->username = $username;
        $this->money = $money;
    }

    public function getUsername() : string{
        return $this->username;
    }

    public function getMoney() : float{
        return $this->money;
    }
}
