<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\text\TextFormat;
use cylexsky\session\SessionManager;
use cylexsky\ui\player\PlayerUIHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TogglesCommand extends Command{

    public const NAME = "toggles";
    public const DESCRIPTION = "Enable and disable your toggles";
    public const USAGE = TextFormat::RED . "/toggles";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, null, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            return;
        }
        $s = SessionManager::getSession($sender->getXuid());
        PlayerUIHandler::sendTogglesUI($s);
    }

}