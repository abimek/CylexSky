<?php
declare(strict_types=1);

namespace cylexsky\main\ranks;

use core\ranks\Rank;
use cylexsky\main\RankTiers;
use cylexsky\utils\Glyphs;
use cylexsky\utils\RankIds;
use pocketmine\utils\TextFormat;

final class Archer extends Rank
{

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::NORMAL_RANK;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return RankTiers::ARCHER;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return RankIds::ARCHER;
    }

    protected function init(): void
    {
        $this->setChatFormat(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::DARK_AQUA . Glyphs::ARCHER . " {name}" . TextFormat::DARK_GRAY . ": " . TextFormat::GRAY . "{msg}");
        $this->setDisplayTag(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::DARK_AQUA . Glyphs::ARCHER . " {name}" . TextFormat::RESET . "{msg}");
    }
}
