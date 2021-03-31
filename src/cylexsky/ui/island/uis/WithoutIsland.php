<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class WithoutIsland extends SimpleForm{

    public function __construct()
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GRAY . "Island");
        $this->addButton(Glyphs::BOX_EXCLAMATION . TextFormat::RED . "Create Island!");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            if ($data === null){
                return;
            }
            $session = SessionManager::getSession($player->getXuid());
            switch ($data){
                case 0:
                    IslandUIHandler::sendCreationUI($session);
                    return;
            }
        };
    }
}