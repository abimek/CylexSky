<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class IslandUI extends SimpleForm{

    private $session;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(Glyphs::ISLAND_ICON . TextFormat::BOLD_GOLD . "Island UI" . Glyphs::ISLAND_ICON);
        $this->addButton(Glyphs::RIGHT_ARROW . TextFormat::BOLD_GRAY . "Settings" . Glyphs::LEFT_ARROW);
        $this->addButton(Glyphs::RIGHT_ARROW . TextFormat::BOLD_GRAY . "Island Permissions" . Glyphs::LEFT_ARROW);
        $this->addButton(Glyphs::RIGHT_ARROW . TextFormat::BOLD_GRAY . "Island Level" . Glyphs::LEFT_ARROW);
        $this->addButton(Glyphs::RIGHT_ARROW . TextFormat::BOLD_GRAY . "Skills" . Glyphs::LEFT_ARROW);
        $this->addButton(Glyphs::RIGHT_ARROW . TextFormat::BOLD_GRAY . "Challenges" . Glyphs::LEFT_ARROW);
        $this->addButton(Glyphs::RIGHT_ARROW . TextFormat::BOLD_GRAY . "Boosters" . Glyphs::LEFT_ARROW);
        $this->addButton(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Exit");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            if ($data === null){
                $this->session->sendNotification("Nothing selected!");
                return;
            }
            switch ($data){
                default:

            }
        };
    }
}