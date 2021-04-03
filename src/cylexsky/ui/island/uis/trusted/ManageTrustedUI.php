<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\trusted;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\island\commands\subcommands\TrustedKickCommand;
use cylexsky\session\PlayerSession;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class ManageTrustedUI extends SimpleForm{

    private $xuid;
    private $name;
    private $session;

    public function __construct(PlayerSession $session, string $xuid)
    {
        parent::__construct($this->getFormResultCallable());
        $this->xuid = $xuid;
        $this->session = $session;
        $name = $session->getIslandObject()->getTrustedModule()->xuidToName($xuid);
        $this->name = $name;
        $this->setTitle(TextFormat::GOLD . $name);
        $this->addButton(TextFormat::BOLD_GREEN . "Permissions");
        $this->addButton(TextFormat::BOLD_RED . "Kick");
        $this->addButton(TextFormat::RED . "Back");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
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
            switch ($data){
                case 0:
                    //TODO PERMISSIONS
                    return;
                case 1:
                    TrustedKickCommand::kick($player, $this->name);
                    return;
                default:
                    IslandUIHandler::sendTrustedMemberList($session);
                    return;
            }
        };
    }
}