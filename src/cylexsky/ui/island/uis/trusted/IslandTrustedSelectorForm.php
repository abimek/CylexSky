<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\trusted;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\island\Island;
use cylexsky\island\IslandManager;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class IslandTrustedSelectorForm extends SimpleForm{

    private $cancel = false;

    private $islands;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(Glyphs::RIGHT_ARROW . TextFormat::BOLD_AQUA . "Trusted Islands" . Glyphs::LEFT_ARROW);
        if ($session->getTrustedModule()->getTrustedCount() === 0){
            $this->cancel = true;
            $this->setContent(Glyphs::BOX_EXCLAMATION . "There are no trusteds on your island!");
            return;
        }
        foreach ($session->getTrustedModule()->getTrustedIslands() as $island){
            if ($island instanceof Island){
                $this->islands[] = $island->getId();
                $this->addButton(Glyphs::ISLAND_ICON . TextFormat::GOLD . $island->getOwnerName() . "'s " . TextFormat::GRAY . "island");
            }
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data = null){
            if ($this->cancel || $data === null){
                return;
            }
            $session = SessionManager::getSession($player->getXuid());
            $island = $this->islands[$data];
            $island = IslandManager::getIsland($island);
            if ($island === null){
                $session->sendNotification("Island Seems to not exist! Weird :/");
                return;
            }
            IslandUIHandler::sendTrustedIslandForm($session, $island);
        };
    }
}