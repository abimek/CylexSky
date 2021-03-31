<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use cylexsky\session\PlayerSession;
use pocketmine\world\Position;

class Teleport{

    private $canTeleport = true;
    private $session;

    public function __construct(PlayerSession $session)
    {
        $this->session = $session;
    }

    public function getSession(): PlayerSession{
        return $this->session;
    }

    public function canTeleport(): bool {
        return $this->canTeleport;
    }

    public function setCanTeleport(bool $value){
        $this->canTeleport = $value;
    }

    public function teleport(Position $Pos){

    }
}