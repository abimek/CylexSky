<?php
declare(strict_types=1);
namespace cylexsky\ui\player\uis\toggles;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\session\modules\Toggles;
use cylexsky\session\PlayerSession;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class TogglesUI extends CustomForm{

    private $session;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(Glyphs::SWORD_LEFT . TextFormat::BOLD_GOLD . "Toggles" . Glyphs::SWORD_RIGHT);
        $toggles = $session->getTogglesModule()->getToggles();
        $this->addLabel(Glyphs::BUBBLE_MESSAGE . TextFormat::AQUA . "Lexy: " . TextFormat::GRAY . "Toggles allow you to customize your game experience!");
        foreach ($toggles as $key => $value){
            ($value) ? $color = TextFormat::GREEN : $color = TextFormat::GRAY;
            $this->addToggle($color . Toggles::TOGGLE_NAMES[$key], $value);
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data=null){
            if ($data === null){
                return;
            }
            array_shift($data);
            $this->session->getTogglesModule()->setToggles($data);
            $this->session->sendGoodNotification("Toggles updated!");
        };
    }
}