<?php
declare(strict_types=1);

namespace cylexsky\ui\island\uis\misc;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use cylexsky\island\modules\SettingsModule;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;
use pocketmine\Server;

class VisitForm extends CustomForm{

    private $session;
    private $online;

    public function __construct(PlayerSession $session)
    {
        parent::__construct($this->getFormResultCallable());
        $this->session = $session;
        $this->setTitle(TextFormat::BOLD_GOLD . "Visit Form");
        $this->addLabel(Glyphs::BOX_EXCLAMATION . " Visit Someone's island");
        $online = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $session = SessionManager::getSession($player->getXuid());
            if ($session->getIslandObject() === null || $session->getIslandObject()->getSettingsModule()->getSetting(SettingsModule::VISITING) === false){
                continue;
            }
            $online[] = $player->getName();
        }
        $this->online = $online;
        $this->addDropdown("Player to Visit: ", $online, 0);
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null){
            if ($data === null){
                return;
            }
            $name = $data[1];
            if (!isset($this->online[$name])){
                return;
            }
            $name = $this->online[$name];
            $session = $this->session;
            if (Server::getInstance()->getPlayerByPrefix($name) === null){
                $session->sendNotification(TextFormat::GRAY . $name . " " . TextFormat::RED . "isn't online!");
                return;
            }
            $s = SessionManager::getSession(Server::getInstance()->getPlayerByPrefix($name)->getXuid());
            if($s->getIslandObject() === null){
                $session->sendNotification(TextFormat::GRAY . $name . TextFormat::GRAY . " is not in an island!");
                return;
            }
            if($s->getIslandObject()->getSettingsModule()->getSetting(SettingsModule::VISITING) === false){
                $session->sendNotification($name . "'s " . "island has visiting disabled!");
                return;
            }
            if (!$session->getTeleportModule()->canTeleport()){
                $session->sendNotification("Unable to teleport!");
                return;
            }
            if ($name === $player->getName()){
                $session->sendNotification("You to teleport to your own island silly!");
                return;
            }
            $island = $s->getIslandObject();
            $island->teleportPlayer($player);
            $session->sendGoodNotification("Teleporting to " . TextFormat::GOLD . $name . "'s " . TextFormat::GREEN . "island...");
        };
    }
}