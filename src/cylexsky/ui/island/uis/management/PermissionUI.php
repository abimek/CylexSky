<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\management;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\island\modules\PermissionModule;
use cylexsky\session\PlayerSession;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class PermissionUI extends CustomForm{

    public const LIST = [
        0 => "Officers",
        1 => "Members",
        2 => "Visitors"
    ];

    public const TO_NORMAL = [
        0 => 2,
        1 => 1,
        2 => 0
    ];

    private $session;
    private $type;
    private $customData = [];

    public function __construct(int $type, PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->type = $type;
        if (!isset(self::LIST[$type])){
            return;
        }
        $is = $session->getIslandObject();
        if ($is === null || ($session->getXuid() !== $is->getOwner() && $session->getIslandObject()->getMembersModule()->isCoOwnerUsername($session->getObject()->getUsername()))){
            return;
        }
        $this->setTitle(Glyphs::ISLAND_ICON . TextFormat::BOLD_YELLOW . self::LIST[$type] . " Permissions" . Glyphs::ISLAND_ICON);
        $this->addLabel(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Enable or disable specific permissions for " . self::LIST[$type]);
        $perms = $is->getPermissionModule();
        if ($type === 0){
            $data = $perms->getOfficerPermissions();
        }elseif($type === 1){
            $data = $perms->getMemberPermissions();
        }else{
            $data = $perms->getGuestPermissions();
        }
        $this->customData = $data;
        foreach ($data as $int => $value){
            $name = PermissionModule::POSSIBLE_PERMISSIONS[$int];
            $this->addToggle(TextFormat::GRAY . $name, $value);
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null){
            if ($data === null){
                $this->session->sendNotification("No data inputed!");
                return;
            }
            if ($this->session->getIslandObject() === null){
                $this->session->sendNotification("You are not in an island");
            }
            if ($this->session->getIslandObject()->getOwner() !== $player->getXuid()){
                $this->session->sendNotification("You are not the owner of the island!");
                return;
            }
            array_shift($data);
            $stuff = [];
            foreach ($this->customData as $int => $datum){
                $stuff[$int] = $data[count($stuff)];
            }
            $this->session->getIslandObject()->getPermissionModule()->setPermissions(self::TO_NORMAL[$this->type], $stuff);
            $this->session->sendGoodNotification("Successfully updated " . TextFormat::GOLD . self::LIST[$this->type] . TextFormat::GREEN . " permissions!");
        };
    }
}