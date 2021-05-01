<?php
declare(strict_types=1);

namespace cylexsky\utils;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

class Sounds{

    public const JOIN_SOUND = "random.login_sound";

    public const LEVEL_UP_SOUND = "random.level_up";
    public const ISLAND_LEVEL_UP_SOUND = "random.island_level_up";

    public const BIRD_CHIRPING_SOUND = "random.bird_chirping_spawn";

    public static function sendSoundPlayer(Player $player, string $sound, float $pitch = 3.0, float $volume = 6.0)
    {
        $pk = new PlaySoundPacket();
        $pos = $player->getPosition();
        $pk->pitch = 3.0;
        $pk->volume = 6.0;
        $pk->soundName = $sound;
        $pk->x = $pos->getX();
        $pk->y = $pos->getY();
        $pk->z = $pos->getZ();
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}