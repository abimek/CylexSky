<?php
declare(strict_types=1);

namespace cylexsky\main\ranks;

use core\main\text\TextFormat;
use core\ranks\Rank;
use cylexsky\main\RankTiers;
use cylexsky\utils\Glyphs;
use cylexsky\utils\RankIds;

final class Elite extends Rank
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
        return RankTiers::ELITE;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return RankIds::ELITE;
    }

    protected function init(): void
    {
        $this->setChatFormat(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::LIGHT_PURPLE . Glyphs::ELITE . " {name}" . TextFormat::DARK_GRAY . ": " . TextFormat::GRAY . "{msg}");
        $this->setDisplayTag(TextFormat::GRAY . "[" . TextFormat::GOLD . "{islevel}" . TextFormat::GRAY . "]" . TextFormat::LIGHT_PURPLE . Glyphs::ELITE . " {name}" . TextFormat::RESET . "{msg}");
    }
}
