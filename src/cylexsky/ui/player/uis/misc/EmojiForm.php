<?php
declare(strict_types=1);

namespace cylexsky\ui\player\uis\misc;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class EmojiForm extends SimpleForm{

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(Glyphs::SMILE_EMOJI . TextFormat::BOLD_GOLD . " Emojis " . Glyphs::SMILE_EMOJI);
        $content = "";
        $content .= Glyphs::BOX_EXCLAMATION . TextFormat::RESET_RED . " Emojis: " . TextFormat::GRAY . " Emojis allow you type in charachters in chat that can be more discriptive then plain words, you can easily do this by typing :<emoji name>:\n";
        $content .= TextFormat::GOLD . "You're emojis: " . "\n";
        foreach ($session->getEmojisModule()->getEmojisMapped() as $key => $emoji){
            $content .= TextFormat::GRAY . "  " . $key . TextFormat::YELLOW . " => " . $emoji . "\n";
        }
        $content .= TextFormat::DARK_GRAY . "=========================\n";
        $content .= TextFormat::RED . "Emojis Not Unlocked: " . "\n";
        foreach ($session->getEmojisModule()->getNotUnlockedEmojisMapped() as $key => $emoji) {
            $content .= TextFormat::DARK_RED . "  " . $key . TextFormat::AQUA . " => " . $emoji . "\n";
        }
        $this->setContent($content);
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data = null){
            return;
        };
    }
}