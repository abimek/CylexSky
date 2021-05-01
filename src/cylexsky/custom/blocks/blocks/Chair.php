<?php
declare(strict_types=1);

namespace cylexsky\custom\blocks\blocks;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\item\ToolTier;

class Chair extends RotatableBlock implements CylexBlockI {

    protected $chairHeight = 1.5;

    public function __construct(BlockIdentifier $idInfo, string $name, BlockBreakInfo $breakInfo)
    {
        parent::__construct($idInfo, $name, $breakInfo);
       // $this->chairHeight = floatval(self::getParameters()["chair_height"]);
    }

    public static function getTrueBreakInfo(): BlockBreakInfo
    {
        return new BlockBreakInfo(1.25, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 21.0);
    }

    public function getChairHeight(): float {
        return $this->chairHeight;
    }
}