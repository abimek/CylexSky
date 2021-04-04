<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\trusted;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\island\modules\PermissionModule;
use cylexsky\session\PlayerSession;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class TrustedIslandPermissions extends CustomForm {

    private $xuid;
    private $name;
    private $session;
    private $datums;

    public function __construct(PlayerSession $session, string $xuid)
    {
        parent::__construct($this->getFormResultCallable());
        $this->xuid = $xuid;
        $this->session = $session;
        $name = $session->getIslandObject()->getTrustedModule()->xuidToName($xuid);
        $this->name = $name;
        $this->setTitle(TextFormat::GOLD . $name);
        $this->addLabel(Glyphs::BOX_EXCLAMATION . TextFormat::GOLD . $name . "'s " . TextFormat::GRAY . "Permissions!");
        $perms = $session->getIslandObject()->getPermissionModule()->getTrustedPermission($xuid);
        $this->datums = $perms;
        foreach ($perms as $int => $value){
            $permName = PermissionModule::POSSIBLE_PERMISSIONS[$int];
            $this->addToggle(TextFormat::GRAY . $permName, $value);
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null){
            if ($data === null){
                return;
            }
            if ($this->session->getIslandObject() === null){
                $this->session->sendNotification("Your island seems to not exist");
                return;
            }
            $is = $this->session->getIslandObject();
            $session = $this->session;
            if (!$session->getIslandObject()->getOwner() === $session->getXuid() && !$session->getIslandObject()->getMembersModule()->isCoOwner($session->getXuid())){
                $session->getPlayer()->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Only island " . TextFormat::RED . "owners and coowners " . TextFormat::GRAY . "can manage trusteds!");
                return;
            }
            if (!$is->getTrustedModule()->isTrusted($this->xuid)){
                $session->sendNotification(TextFormat::GOLD . $this->name . TextFormat::GRAY . " is nolonger trusted on your island!");
                return;
            }
            array_shift($data);
            $stuff = [];
            foreach ($this->datums as $int => $datum){
                $stuff[$int] = $data[count($stuff)];
            }
            $this->session->getIslandObject()->getPermissionModule()->setTrustedPermission($this->xuid, $stuff);
            $this->session->sendGoodNotification("Successfully updated " . TextFormat::GOLD . $this->name . "'s " . TextFormat::GREEN . " permissions!");
        };
    }
}