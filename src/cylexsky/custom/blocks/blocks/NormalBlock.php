<?php
declare(strict_types=1);

namespace cylexsky\custom\blocks\blocks;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\ToolTier;

class NormalBlock extends Opaque implements CylexBlockI {

    public function __construct(BlockIdentifier $idInfo, string $name, BlockBreakInfo $breakInfo)
    {
        parent::__construct($idInfo, $name, $breakInfo);
    }

    public static function getTrueBreakInfo(): BlockBreakInfo
    {
        return new BlockBreakInfo(1.25, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 21.0);
    }
}