<?php
declare(strict_types=1);

namespace cylexsky\ui\island;

use core\forms\formapi\Form;
use cylexsky\island\Island;
use cylexsky\session\PlayerSession;
use cylexsky\ui\InventoryUI;
use cylexsky\ui\island\uis\InviteTrustedUI;
use cylexsky\ui\island\uis\InviteUI;
use cylexsky\ui\island\uis\InvUis\InvIslandUi;
use cylexsky\ui\island\uis\IslandCreationForm;
use cylexsky\ui\island\uis\IslandInfoUI;
use cylexsky\ui\island\uis\IslandUI;
use cylexsky\ui\island\uis\management\PermissionUI;
use cylexsky\ui\island\uis\management\SettingsUI;
use cylexsky\ui\island\uis\ManagementUI;
use cylexsky\ui\island\uis\management\PermissionSelectUI;
use cylexsky\ui\island\uis\misc\VisitForm;
use cylexsky\ui\island\uis\reset\IslandResetListForm;
use cylexsky\ui\island\uis\TopIslandsUI;
use cylexsky\ui\island\uis\trusted\IslandTrustedSelectorForm;
use cylexsky\ui\island\uis\trusted\ManageTrustedUI;
use cylexsky\ui\island\uis\trusted\TrustedIslandForm;
use cylexsky\ui\island\uis\trusted\TrustedIslandPermissions;
use cylexsky\ui\island\uis\trusted\TrustedMembersListForm;
use cylexsky\ui\island\uis\upgrades\UpgradeForm;
use cylexsky\ui\island\uis\upgrades\UpgradeTypeForm;
use cylexsky\ui\island\uis\warps\AdminWarpUI;
use cylexsky\ui\island\uis\warps\WarpCreateUI;
use cylexsky\ui\island\uis\warps\WarpsUI;
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

    public static function sendWarpCreateUI(PlayerSession $session){
        self::sendUI($session, new WarpCreateUI($session));
    }

    public static function sendIslandUpgradeTypeForm(PlayerSession $session, int $type){
        self::sendUI($session, new UpgradeTypeForm($session, $type));
    }

    public static function sendIslandUpgradeForm(PlayerSession $session){
        self::sendUI($session, new UpgradeForm($session));
    }

    public static function sendVisitForm(PlayerSession $session){
        self::sendUI($session, new VisitForm($session));
    }

    public static function sendIslandResetUIForrm(PlayerSession $session){
        self::sendUI($session, new IslandResetListForm($session));
    }

    public static function sendIslandTopForm(PlayerSession $session){
        self::sendUI($session, new TopIslandsUI($session));
    }

    public static function sendAdminWarpUI(PlayerSession $session, string $name){
        self::sendUI($session, new AdminWarpUI($session, $name));
    }

    public static function sendIslandTrustedSelectForm(PlayerSession $session){
        self::sendUI($session, new IslandTrustedSelectorForm($session));
    }

    public static function sendWarpUI(PlayerSession $session){
        self::sendUI($session, new WarpsUI($session));
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
        var_dump($session->getUiType());
        if ($session->getUiType() === PlayerSession::UI_INV){
            self::sendUI($session, new InvIslandUi($session->getPlayer()));
        }else {
            self::sendUI($session, new IslandUI($session));
        }
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

    public static function sendUI(PlayerSession $session, $form){
        if ($form instanceof Form){
            $session->getPlayer()->sendForm($form);
        }
        if ($form instanceof InventoryUI){
            $form->send(true);
        }
    }

}