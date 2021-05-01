<?php
declare(strict_types=1);

namespace cylexsky\ui\player\uis\economy\bank;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class BankDepositUI extends CustomForm{

    private $session;

    public function __construct(PlayerSession $session)
    {
        $this->session = $session;
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(Glyphs::GOLD_COIN . TextFormat::BOLD_RED . "Deposit" . Glyphs::GOLD_COIN);
        $money = $session->getMoneyModule()->getMoney();
        $bank = $session->getMoneyModule()->getBankValue();
        $bankLimit = $session->getMoneyModule()->getBankLimit();
        $content = TextFormat::RED . "Banker: " . Glyphs::BUBBLE_MESSAGE . TextFormat::GRAY . "Deposit money to your bank and protect it from your clumsy self!" ."\n\n";
        $content .= TextFormat::GOLD . "Amount in Bank: " . TextFormat::GRAY . $bank . Glyphs::GOLD_COIN . "\n";
        $content .= TextFormat::GOLD . "Bank Limit: " . TextFormat::GRAY . $bankLimit . Glyphs::GOLD_COIN . "\n";
        $content .= TextFormat::GOLD . "You're Money: " . TextFormat::GRAY . $money . Glyphs::GOLD_COIN . "\n";
        $this->addLabel($content);
        $this->addSlider(Glyphs::GOLD_COIN . TextFormat::GRAY . "Deposit Amount", 0, $session->getMoneyModule()->getCurrentDipositiableAmount(), 1, 0);
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null){
            if ($data === null){
                return;
            }
            $amount = $data[1];
            $this->session->getMoneyModule()->addToBank(intval($amount), true);
        };
    }
}