<?php
declare(strict_types=1);

namespace cylexsky\custom\blocks\blocks;

use pocketmine\block\BlockBreakInfo;

interface CylexBlockI{

    public static function getTrueBreakInfo(): BlockBreakInfo;
}