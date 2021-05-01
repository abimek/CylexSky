<?php
declare(strict_types=1);

namespace cylexsky\ui\player\uis\economy\bank;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\session\PlayerSession;
use cylexsky\utils\Glyphs;
use cylexsky\utils\Utils;
use pocketmine\player\Player;

class BankUI extends SimpleForm{

    private $session;
    private $maxed;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(Glyphs::GOLD_COIN . TextFormat::BOLD_GOLD . "Banker" . Glyphs::GOLD_COIN);
        $money = $session->getMoneyModule()->getMoney();
        $bank = $session->getMoneyModule()->getBankValue();
        $bankLimit = $session->getMoneyModule()->getBankLimit();
        $content = TextFormat::RED . "Banker: " . Glyphs::BUBBLE_MESSAGE . TextFormat::GRAY . "I'm sure you dont want to lose your money, keep it safe with me!" ."\n\n";
        $content .= TextFormat::GOLD . "Amount in Bank: " . TextFormat::GRAY . $bank .  " " . Glyphs::GOLD_COIN . "\n";
        $content .= TextFormat::GOLD . "Bank Limit: " . TextFormat::GRAY . $bankLimit .  " " . Glyphs::GOLD_COIN . "\n";
        $content .= TextFormat::GOLD . "You're Money: " . TextFormat::GRAY . $money .  " " . Glyphs::GOLD_COIN . "\n";
        $content .= TextFormat::AQUA . "Bank Level: " . TextFormat::GOLD . Utils::numberToRomanRepresentation($session->getMoneyModule()->getBankLimitDirect());
        $this->setContent($content);
        $this->addButton(Glyphs::GREEN_BOX_EXCLAMATION . " " . TextFormat::BOLD_DARK_GRAY . "Deposit");
        $this->addButton(Glyphs::BOX_EXCLAMATION . " " . TextFormat::BOLD_DARK_GRAY . "Withdraw");
        $this->maxed = $session->getMoneyModule()->isBankLimitMaxed();
        if (!$this->maxed){
            $this->addButton(Glyphs::GREEN_BOX_EXCLAMATION . " " . TextFormat::BOLD_GOLD . "Upgrade: " . $session->getMoneyModule()->getNextToUpgrade() . " " . Glyphs::OPAL);
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data = null){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    $player->sendForm(new BankDepositUI($this->session));
                    return;
                case 1:
                    $player->sendForm(new BankWithdrawUI($this->session));
                    return;
                case 2:
                    if (!$this->maxed){
                        $this->session->getMoneyModule()->upgradeBankLimit();
                    }
            }
        };
    }
}