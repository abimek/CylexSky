<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\island\IslandManager;
use cylexsky\session\PlayerSession;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\utils\Glyphs;
use cylexsky\utils\Utils;
use pocketmine\player\Player;

class TopIslandsUI extends SimpleForm{

    private $session;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(TextFormat::BOLD_RED . "Top Islands");
        if ($session->getIslandObject() === null){
            $this->setTitle(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "You are not a member of an island");
            return;
        }
        $content = "";
        $content .= Glyphs::GREEN_BOX_EXCLAMATION . TextFormat::GRAY . "Top Islands!\n";
        $first = false;
        if (count(IslandManager::getTopIslands()) === 0){
            $this->setContent(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "\nNo current islands, be the first to get on this list!");
        }
        foreach (IslandManager::getTopIslands() as $int => $data){
            $int += 1;
            $roman = Utils::numberToRomanRepresentation($int);
            if ($first === false){
                $first = true;
                $content .= Glyphs::CROWN . TextFormat::GOLD . $roman . TextFormat::RED . " " . $data[0] . ":" . TextFormat::GRAY . $data[1] . "\n";
            }else{
                $content .= TextFormat::YELLOW . $roman . TextFormat::RED . " " . $data[0] . ":" . TextFormat::GRAY . $data[1] . "\n";
            }
            $this->setContent($content);

        }
        $this->addButton(TextFormat::RED . "Back");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data){
            if ($data === null){
                return;
            }
            if ($data === 0){
                IslandUIHandler::sendIslandUI($this->session);
            }
        };
    }
}