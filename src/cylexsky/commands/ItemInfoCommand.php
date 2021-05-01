<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\text\TextFormat;
use core\ranks\types\RankTypes;
use cylexsky\session\SessionManager;
use cylexsky\utils\Glyphs;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ItemInfoCommand extends Command{

    public const NAME = "iteminfo";
    public const DESCRIPTION = "See the items info";
    public const USAGE = TextFormat::RED . "/iteminfo";

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
        if ($s->getRank()->getType() === RankTypes::NORMAL_RANK){
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        if ($item->getId() !== 0){
            $serialized = $item->jsonSerialize();
            $sender->sendMessage(Glyphs::C_RIGHT_ARROW . TextFormat::GOLD . "Id: " . TextFormat::GRAY . $serialized["id"]);
            if (isset($serialized["damage"])){
                $sender->sendMessage(Glyphs::C_RIGHT_ARROW . TextFormat::GOLD . "Damage: " . TextFormat::GRAY . $serialized["damage"]);
            }
            if (isset($serialized["count"])){
                $sender->sendMessage(Glyphs::C_RIGHT_ARROW . TextFormat::GOLD . "Count: " . TextFormat::GRAY . $serialized["count"]);
            }
            if (isset($serialized["nbt_64"])){
                $sender->sendMessage(Glyphs::C_RIGHT_ARROW . TextFormat::GOLD . "NBT: " . TextFormat::GRAY . $serialized["nbt_64"]);
            }
        }
    }

}