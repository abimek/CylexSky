<?php
declare(strict_types=1);

namespace cylexsky\ui\island;

use cylexsky\session\PlayerSession;
use cylexsky\ui\island\uis\InviteUI;
use cylexsky\ui\island\uis\IslandCreationForm;
use cylexsky\ui\island\uis\IslandUI;
use cylexsky\ui\island\uis\WithoutIsland;

class IslandUIHandler{
    public static function sendCreationUI(PlayerSession $session){
        $session->getPlayer()->sendForm(new IslandCreationForm($session));
    }

    public static function sendInviteUI(PlayerSession $session){
        $session->getPlayer()->sendForm(new InviteUI($session));
    }

    public static function sendWithoutIsland(PlayerSession $session){
        $session->getPlayer()->sendForm(new WithoutIsland());
    }

    public static function sendIslandUI(PlayerSession $session){
        $session->getPlayer()->sendForm(new IslandUI($session));
    }
}