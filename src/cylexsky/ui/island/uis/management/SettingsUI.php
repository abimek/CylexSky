<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\management;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\island\modules\SettingsModule;
use cylexsky\session\PlayerSession;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class SettingsUI extends CustomForm{

    private $session;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $is = $session->getIslandObject();
        if ($is === null || ($session->getXuid() !== $is->getOwner() && $session->getIslandObject()->getMembersModule()->isCoOwnerUsername($session->getObject()->getUsername()))){
            return;
        }
        $this->setTitle(Glyphs::ISLAND_ICON . TextFormat::BOLD_RED . "Island Settings" . Glyphs::ISLAND_ICON);
        $this->addLabel(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Enable or disable specific settings on your island " . Glyphs::ISLAND_ICON . " to make your experience more enjoyable!");
        foreach ($is->getSettingsModule()->getSettings() as $int => $value){
            $name = SettingsModule::INT_TO_NAME[$int];
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
            $this->session->getIslandObject()->getSettingsModule()->setSettings($data);
            $this->session->sendGoodNotification("Successfully updated settings");
        };
    }
}