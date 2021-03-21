<?php
declare(strict_types=1);

namespace cylexsky\misc\join;

use cylexsky\worlds\worlds\MainWorld;
use pocketmine\player\Player;

class JoinHandler{

    public static function onJoin(Player $player){
        MainWorld::teleport($player);
        self::initializeSession();
        self::sendJoinMessages();
        self::sendJoinSound();
    }

    public static function initialJoin(Player $player){
        MainWorld::teleport($player);
    }

    public static function sendJoinMessages(){
    }

    public static function sendJoinSound(){
    }

    public static function initializeSession(){
    }


}