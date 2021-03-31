<?php
declare(strict_types=1);

namespace cylexsky\island\creation;

use pocketmine\block\BlockLegacyIds;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

class IslandGenerator extends Generator{

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        $chunk->setFullBlock(0, 70, 0, BlockLegacyIds::CHEST);
        return;
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {

        return;
    }
}