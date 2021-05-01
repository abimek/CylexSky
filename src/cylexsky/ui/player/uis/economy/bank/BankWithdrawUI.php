<?php
declare(strict_types=1);

namespace cylexsky\ui\player\uis\economy\bank;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class BankWithdrawUI extends CustomForm{

    private $session;

    public function __construct(PlayerSession $session)
    {
        $this->session = $session;
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(Glyphs::GOLD_COIN . TextFormat::BOLD_RED . "Withdraw" . Glyphs::GOLD_COIN);
        $money = $session->getMoneyModule()->getMoney();
        $bank = $session->getMoneyModule()->getBankValue();
        $bankLimit = $session->getMoneyModule()->getBankLimit();
        $content = TextFormat::RED . "Banker: " . Glyphs::BUBBLE_MESSAGE . TextFormat::GRAY . "Withdraw money from your bank, but dont die or you'll lose a percentage of it!" ."\n\n";
        $content .= TextFormat::GOLD . "Amount in Bank: " . TextFormat::GRAY . $bank . Glyphs::GOLD_COIN . "\n";
        $content .= TextFormat::GOLD . "Bank Limit: " . TextFormat::GRAY . $bankLimit . Glyphs::GOLD_COIN . "\n";
        $content .= TextFormat::GOLD . "You're Money: " . TextFormat::GRAY . $money . Glyphs::GOLD_COIN . "\n";
        $this->addLabel($content);
        $this->addSlider(Glyphs::GOLD_COIN . TextFormat::GRAY . "Withdraw Amount", 0, $bank, 1, 0);
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null){
            if ($data === null){
                return;
            }
            $amount = abs($data[1]);
            $this->session->getMoneyModule()->takeFromBank(intval($amount), true);
        };
    }
}