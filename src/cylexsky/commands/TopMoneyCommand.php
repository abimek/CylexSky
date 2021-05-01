<?php
declare(strict_types=1);

namespace cylexsky\commands;

use core\main\text\TextFormat;
use cylexsky\misc\economy\EconomyManager;
use cylexsky\utils\Glyphs;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class TopMoneyCommand extends Command{

    public const NAME = "topmoney";
    public const DESCRIPTION = "See the richest people on the server";
    public const USAGE = TextFormat::RED . "/topmoney";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, null, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            return;
        }
        $page = (int)array_shift($args);

        $max = count(EconomyManager::getInstance()->getAllMoney());
        $maxPage = ceil($max / 5);
        $page = min($maxPage, $page);
        $page = max(1, $page);
        $page = intval($page);
        $server = Server::getInstance();
        if ($page > $maxPage){
            $sender->sendMessage(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . " Page " . TextFormat::RED . $page . TextFormat::GRAY . " does not exist!");
            return;
        }
        $ops = [];
        $banned = [];
        foreach($server->getNameBans()->getEntries() as $entry){
            $banned[strtolower($entry->getName())] = true;
        }

        foreach($server->getOps()->getAll() as $op => $tmp){
            $ops[strtolower($op)] = true;
        }

        $sender->sendMessage(TextFormat::YELLOW . "■■■■■■■■■■■■■■■■■■■■■■■■■■");
        for($i = 1; $i <= 5; $i++){
            $rank = (5 * ($page - 1)) + $i;
            if($rank > $max){
                break;
            }
            $player = EconomyManager::getInstance()->getPlayerByRank($rank);
            $line = TextFormat::RED . "[" . TextFormat::GREEN . $rank . TextFormat::RED . "] " . TextFormat::GRAY . $player . " : " . TextFormat::GOLD . EconomyManager::getInstance()->myMoney($player) . Glyphs::GOLD_COIN;
            $sender->sendMessage($line);
        }
        $sender->sendMessage(TextFormat::YELLOW . "■■■■■■■■■■■■■■■■■■■■■■■■■■");
        return;
    }

}