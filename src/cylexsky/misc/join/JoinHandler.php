<?php
declare(strict_types=1);

namespace cylexsky\misc\join;

use pocketmine\player\Player;

class JoinHandler{

    public static function onJoin(Player $player){
        //TODO TELEPORT TO SPAWN AND SEND MESSAGEs
        self::initializeSession();
        self::sendJoinMessages();
        self::sendJoinSound();
    }

    public static function sendJoinMessages(){

    }

    public static function sendJoinSound(){

    }

    public static function initializeSession(){

    }

}