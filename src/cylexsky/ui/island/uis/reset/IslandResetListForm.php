<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\reset;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use core\ranks\RankManager;
use cylexsky\island\creation\IslandCreationHandler;
use cylexsky\island\IslandManager;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class IslandResetListForm extends SimpleForm{

    private $info = [];

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(Glyphs::OPEN_BOOK . TextFormat::BOLD_RED . "Island Creation" . Glyphs::OPEN_BOOK);
        $this->setContent(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Better Island Generators can unlocked with higher ranks!");
        foreach (IslandCreationHandler::getTypes() as $name => $data){
            if ($data[1] > RankManager::getRank($session->getObject()->getRank())->getLevel()){
                continue;
            }
            $this->info[] = $data;
            $this->addButton(TextFormat::BOLD_YELLOW . TextFormat::BOLD_GRAY . $data[0]);
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            $session = SessionManager::getSession($player->getXuid());
            if ($data === null){
                $session->sendNotification("You forgot to create an island!");
                return;
            }
            if ($session->getIslandObject() === null || $session->getIslandObject()->getOwner() !== $player->getXuid()){
                return;
            }
            $selected = $this->info[$data];
            IslandManager::resetIsland($session, new $selected[2]);
            $session->sendGoodNotification("Successfully " . TextFormat::RED . "restarted " . TextFormat::GREEN . "your island!");
        };
    }
}