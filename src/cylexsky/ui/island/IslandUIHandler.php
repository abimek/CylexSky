<?php
declare(strict_types=1);

namespace cylexsky\ui\island;

use core\forms\formapi\Form;
use cylexsky\island\Island;
use cylexsky\session\PlayerSession;
use cylexsky\ui\island\uis\InviteTrustedUI;
use cylexsky\ui\island\uis\InviteUI;
use cylexsky\ui\island\uis\IslandCreationForm;
use cylexsky\ui\island\uis\IslandInfoUI;
use cylexsky\ui\island\uis\IslandUI;
use cylexsky\ui\island\uis\management\PermissionUI;
use cylexsky\ui\island\uis\management\SettingsUI;
use cylexsky\ui\island\uis\ManagementUI;
use cylexsky\ui\island\uis\management\PermissionSelectUI;
use cylexsky\ui\island\uis\trusted\IslandTrustedSelectorForm;
use cylexsky\ui\island\uis\trusted\ManageTrustedUI;
use cylexsky\ui\island\uis\trusted\TrustedIslandForm;
use cylexsky\ui\island\uis\trusted\TrustedIslandPermissions;
use cylexsky\ui\island\uis\trusted\TrustedMembersListForm;
use cylexsky\ui\island\uis\WithoutIsland;

class IslandUIHandler{
    public static function sendCreationUI(PlayerSession $session){
        self::sendUI($session, new IslandCreationForm($session));
    }

    public static function sendInviteUI(PlayerSession $session){
        self::sendUI($session, new InviteUI($session));
    }

    public static function sendInviteTrustedUI(PlayerSession $session){
        self::sendUI($session, new InviteTrustedUI($session));
    }

    public static function sendIslandTrustedSelectForm(PlayerSession $session){
        self::sendUI($session, new IslandTrustedSelectorForm($session));
    }

    public static function sendManageTrustedForm(PlayerSession $session, string $xuid){
        self::sendUI($session, new ManageTrustedUI($session, $xuid));
    }

    public static function sendIslandInfoForm(PlayerSession $session, bool $back = true){
        self::sendUI($session, new IslandInfoUI($session, $back));
    }

    public static function sendTrustedIslandPermissions(PlayerSession $session, string $xuid){
        self::sendUI($session, new TrustedIslandPermissions($session, $xuid));
    }

    public static function sendTrustedIslandForm(PlayerSession $session, Island $island){
        self::sendUI($session, new TrustedIslandForm($session, $island));
    }

    public static function sendWithoutIsland(PlayerSession $session){
        self::sendUI($session, new WithoutIsland());
    }

    public static function sendIslandUI(PlayerSession $session){
        self::sendUI($session, new IslandUI($session));
    }

    public static function sendManagementUI(PlayerSession $session){
        self::sendUI($session, new ManagementUI($session));
    }

    public static function sendSettingsUI(PlayerSession $session){
        self::sendUI($session, new SettingsUI($session));
    }

    public static function sendTrustedMemberList(PlayerSession $session){
        self::sendUI($session, new TrustedMembersListForm($session));
    }

    public static function sendPermissionSelectUI(PlayerSession $session){
        self::sendUI($session, new PermissionSelectUI($session));
    }

    public static function sendPermissionsUI(int $type, PlayerSession $session){
        self::sendUI($session, new PermissionUI($type, $session));
    }


    public static function sendUI(PlayerSession $session, Form $form){
        $session->getPlayer()->sendForm($form);
    }

}