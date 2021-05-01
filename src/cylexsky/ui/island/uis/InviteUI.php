<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\island\modules\PermissionModule;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;
use pocketmine\Server;

class InviteUI extends CustomForm{

    private $session;

    private $canInvite = false;

    public function __construct(PlayerSession $session)
    {
        $this->session = $session;
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(Glyphs::SWORD_RIGHT . TextFormat::BOLD_YELLOW . "Island Invitations" . Glyphs::SWORD_LEFT);
        if ($session->getIslandObject() === null){
            $this->addLabel(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "You are not a member of an island!");
            return;
        }
        if ($session->getIslandObject()->getTutorialModule()->inTutorial()){
            $this->addLabel(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "You are currently in tutorial mode, finsih the tutorial to gain the ability to add memebers to your island!");
            return;
        }
        if ($session->getIslandObject()->getMembersModule()->isMemberLimitReached()){
            $this->addLabel(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Member limit reached, you can " . TextFormat::GOLD . "upgrade " . TextFormat::GRAY . "the member limit through island progression!");
            return;
        }
        $island = $session->getIslandObject();
        if ($session->getIslandObject()->getPermissionModule()->playerHasPermission(PermissionModule::PERMISSION_INVITE, $session)){
            $this->canInvite = true;
            $this->addLabel(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Invite someone to " . TextFormat::GOLD . $session->getIslandObject()->getOwnerName() . "'s " . TextFormat::GRAY . "island!");
            $this->addLabel(Glyphs::BOX_EXCLAMATION . TextFormat::YELLOW . $island->getMembersModule()->getMemberCount() . "/" . TextFormat::RED . $island->getMembersModule()->getMemberLimit() . TextFormat::GRAY . " members in your island!");
            $this->addInput(Glyphs::OPEN_BOOK . TextFormat::GRAY . "Player Name");
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data){
            if (!$this->canInvite){
                return;
            }
            if ($data === null || !isset($data[2]) || $data[2] === ""){
                $this->session->sendNotification("No input was given!");
                return;
            }
            $name = $data[2];
            $p2 = Server::getInstance()->getPlayerExact($name);
            if ($p2 === null){
                $this->session->sendNotification(TextFormat::GRAY . "The player " . TextFormat::RED . $name . TextFormat::GRAY ." is not online!");
                return;
            }
            $session = $this->session;
            if ($session->getIslandObject()->getMembersModule()->isMemberLimitReached()){
                $this->addLabel(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Member limit reached, you can " . TextFormat::GOLD . "upgrade " . TextFormat::GRAY . "the member limit through island progression!");
                return;
            }
            $session2 = SessionManager::getSession($p2->getXuid());
            if ($session2->getIslandObject() !== null){
                $this->session->sendNotification("The player " . TextFormat::AQUA . $name . TextFormat::GRAY. " is already in an island!");
                return;
            }
            $session2->getRequestModule()->inviteToIsland($this->session);
            $this->session->sendGoodNotification("Successfully invited " . TextFormat::AQUA . $name . TextFormat::GREEN . " to your island!");
        };
    }
}