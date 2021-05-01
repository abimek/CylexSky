<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\island\commands\subcommands\MembersCommand;
use cylexsky\session\PlayerSession;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class IslandUI extends SimpleForm{

    private $session;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(Glyphs::SPARKLE . TextFormat::BOLD_GOLD . "Island UI" . Glyphs::SPARKLE);
        $this->addButton(Glyphs::ISLAND_ICON . TextFormat::BOLD_GREEN . "Teleport to Island!");
        $this->addButton( TextFormat::RED . "Upgrade" );
        $this->addButton(TextFormat::RED . "Warps");
        $this->addButton(TextFormat::RED . "Info");
        $this->addButton(TextFormat::RED . "Members");
        $this->addButton( TextFormat::RED . "Challenges/Quests" );
        $this->addButton(TextFormat::RED . "Management");
        $this->addButton(TextFormat::RED . "Top Islands");
        $this->addButton(TextFormat::RED . "Visit");
        if ($session->getMiscModule()->inIslandChat()){
            $this->addButton(TextFormat::YELLOW . "Leave Island Chat");
        }else{
            $this->addButton(TextFormat::GREEN . "Enter Island Chat");
        }
        $this->addButton(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Exit");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            if ($data === null){
                return;
            }
            $is = $this->session->getIslandObject();
            if ($is === null){
                $this->session->sendNotification("You're not in an island!");
                return;
            }
            $session = $this->session;
            switch ($data){
                case 0:
                    if ($session->getTeleportModule()->canTeleport() === false){
                        $session->sendNotification("Unable to teleport!");
                        return;
                    }
                    $session->getIslandObject()->teleportPlayer($player);
                    return;
                case 1:
                    IslandUIHandler::sendIslandUpgradeForm($session);
                    return;
                case 2:
                    IslandUIHandler::sendWarpUI($session);
                    return;
                case 3:
                    IslandUIHandler::sendIslandInfoForm($session);
                    return;
                case 4:
                    MembersCommand::sendMembersInUI($player);
                    return;
                case 6:
                    if ($player->getXuid() !== $session->getIslandObject()->getOwner() && !$session->getIslandObject()->getMembersModule()->isCoOwner($player->getXuid())){
                        $session->sendNotification("Only island " . TextFormat::GOLD . "owners and coowners" . TextFormat::GRAY . " can manage the island!");
                        return;
                    }
                    IslandUIHandler::sendManagementUI($this->session);
                    return;
                case 7:
                    IslandUIHandler::sendVisitForm($session);
                    return;
                case 8:
                    IslandUIHandler::sendIslandTopForm($this->session);
                    return;
                case 9:
                    $session->getMiscModule()->toggleIslandChat();
                default:

            }
        };
    }
}