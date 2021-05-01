<?php
declare(strict_types=1);

namespace cylexsky\custom\blocks\blocks;

use pocketmine\block\Opaque;
use customies\block\StateCarrier;
use pocketmine\block\utils\BlockDataSerializer;
use pocketmine\block\utils\HorizontalFacingTrait;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\BlockTransaction;
use function print_r;
use function var_dump;

class RotatableBlock extends Opaque implements StateCarrier{
    use HorizontalFacingTrait;

    protected function writeStateToMeta(): int {
        $facing = [
                Facing::SOUTH => 1,
                Facing::WEST => 2,
                Facing::NORTH => 0,
                Facing::EAST => 3
            ][$this->facing] ?? 0;
        return $facing;
    }

    public function readStateFromData(int $id, int $stateMeta): void {
        $this->facing = BlockDataSerializer::readLegacyHorizontalFacing($stateMeta & 0x03);
    }

    public function getStateBitmask() : int{
        return 0b111;
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool {
        if($player !== null){
            //$this->facing = Facing::opposite($player->getHorizontalFacing());
            $player->sendMessage(TextFormat::BLUE . "Chair rotation: " . TextFormat::RESET . [0, 90, 180, 270][$player->getHorizontalFacing() - 2]);
            $player->sendMessage(TextFormat::BLUE . "Your rotation: " . TextFormat::RESET . $player->getLocation()->yaw);
            $player->sendMessage(TextFormat::BLUE . "Your direction: ". TextFormat::RESET . $player->getHorizontalFacing() - 2);
            $this->facing = $player->getHorizontalFacing();
        }

        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function getPermutations() : ListTag{
        return new ListTag(array_map(static fn(int $i) => CompoundTag::create()
            ->setTag("components", CompoundTag::create()
                ->setTag("minecraft:rotation", CompoundTag::create()
                    ->setFloat("x", 0)
                    ->setFloat("y", [180, 0, 270, 90][$i])
                    //->setFloat("y", [0, 90, 180, 270][$i])
                    ->setFloat("z", 0)
                )
            )
            ->setString("condition", "query.block_property('customies:rotation') == " . $i), [0, 1, 2, 3]));
    }

    public function getProperties() : ListTag{
        return new ListTag([
            CompoundTag::create()
                ->setTag("enum", new ListTag(array_map(static fn(int $i) => new IntTag($i), [0, 1, 2, 3])))
                ->setString("name", "customies:rotation")
        ]);
    }

    public function getStates() : array{
        return [["customies:rotation" => new IntTag(0)], ["customies:rotation" => new IntTag(1)], ["customies:rotation" => new IntTag(2)], ["customies:rotation" => new IntTag(3)]];
    }
}