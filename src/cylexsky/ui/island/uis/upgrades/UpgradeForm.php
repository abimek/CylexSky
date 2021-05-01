<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\upgrades;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\island\commands\subcommands\MembersCommand;
use cylexsky\session\PlayerSession;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use cylexsky\utils\Utils;
use pocketmine\player\Player;

class UpgradeForm extends SimpleForm{

    private $session;
    private $cancel = false;
    private $prestige = false;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(TextFormat::GOLD . "Island Upgrades");
        if ($session->getIslandObject() === null){
            $this->cancel = true;
            return;
        }
        $is = $session->getIslandObject();
        $this->setTitle(Glyphs::GOLD_MEDAL . TextFormat::BOLD_GOLD . "Island Upgrades" . Glyphs::GOLD_MEDAL);
        if ($is->getLevelModule()->hasEnoughLevelToPrestige() && !$is->getLevelModule()->isPrestigeMaxed()){
            $this->prestige = true;
            $p = Utils::numberToRomanRepresentation($is->getLevelModule()->getPrestige());
            $n= Utils::numberToRomanRepresentation($is->getLevelModule()->getPrestige() + 1);

            if ($is->getLevelModule()->canPrestige()){
                $this->addButton(TextFormat::BOLD_GREEN . "Prestige: " . TextFormat::RESET_GRAY . $n . Glyphs::RIGHT_ARROW . TextFormat::RED . $n . TextFormat::YELLOW . "Shards: " . TextFormat::DARK_GRAY . $is->getLevelModule()->getPrestigeShardsForNextPrestige());
            }else{
                $this->addButton(TextFormat::BOLD_RED . "Prestige: " . TextFormat::RESET_DARK_GRAY . $is->getLevelModule()->getPrestigeShardsForNextPrestige());
            }
        }
        $this->addButton( TextFormat::BOLD_RED . "Minion Limit");
        $this->addButton(TextFormat::BOLD_RED . "Spawner Limit");
        $this->addButton(TextFormat::BOLD_RED . "Spawner Types");
        $this->addButton(TextFormat::BOLD_RED . "Resource Node Limit");
        $this->addButton(TextFormat::BOLD_RED . "Resource Node Types");
        $this->addButton(TextFormat::BOLD_RED . "Hopper Limit");
        $this->addButton(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Exit");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            if ($data === null || $this->cancel){
                return;
            }
            $is = $this->session->getIslandObject();
            if ($is === null){
                $this->session->sendNotification("You're not in an island!");
                return;
            }
            if ($this->prestige){
                if ($data === 0){
                    $this->session->getIslandObject()->getLevelModule()->prestige();
                    return;
                }
                if ($data === 7){
                    IslandUIHandler::sendIslandUI($this->session);
                    return;
                }
                $session = $this->session;
                IslandUIHandler::sendIslandUpgradeTypeForm($session, $data-1);
                return;
            }
            if ($data === 6){
                IslandUIHandler::sendIslandUI($this->session);
                return;
            }
            $session = $this->session;
            IslandUIHandler::sendIslandUpgradeTypeForm($session, $data);
        };
    }
}