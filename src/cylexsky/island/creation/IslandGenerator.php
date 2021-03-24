<?php
declare(strict_types=1);

namespace cylexsky\island\creation;

use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

class IslandGenerator extends Generator{

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        return;
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        return;
    }
}