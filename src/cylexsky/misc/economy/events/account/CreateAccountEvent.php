<?php
declare(strict_types=1);

namespace cylexsky\misc\economy\events\account;

use cylexsky\misc\economy\EconomyManager;
use cylexsky\misc\economy\events\EconomyAPIEvent;

class CreateAccountEvent extends EconomyAPIEvent {

    public static $handlerList;

    /** @var string */
    private $username;

    /** @var float */
    private $defaultMoney;

    public function __construct(EconomyManager $plugin, string $username, float $defaultMoney, string $issuer){
        parent::__construct($plugin, $issuer);
        $this->username = $username;
        $this->defaultMoney = $defaultMoney;
    }

    public function getUsername() : string{
        return $this->username;
    }

    public function setDefaultMoney(float $money) : void{
        $this->defaultMoney = $money;
    }

    public function getDefaultMoney() : float{
        return $this->defaultMoney;
    }
}