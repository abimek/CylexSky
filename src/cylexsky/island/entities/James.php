<?php
declare(strict_types=1);

namespace cylexsky\island\entities;

use core\forms\entity\EntityFormTrait;
use core\main\text\TextFormat;
use cylexsky\island\Island;
use cylexsky\island\IslandManager;
use cylexsky\utils\Glyphs;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class James extends Human{

    use EntityFormTrait;

    public const ISLAND_ID = "island_id";

    private $islandId;
    protected $gravity = 0.0;

    private $num = 0;

    public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $skin, $nbt);
    }

    public function initEntity(CompoundTag $nbt): void
    {
        if ($nbt->hasTag(self::ISLAND_ID)){
            $this->islandId = $nbt->getString(self::ISLAND_ID);
        }
        $this->initEntityForm(Glyphs::JERRY_32 . TextFormat::BOLD_GOLD . "James!");
        $this->setContent(TextFormat::RED . "Welcome back to your island on " . TextFormat::BOLD_GOLD . "Cylex" . TextFormat::AQUA . "Sky!" . TextFormat::RESET_RED . "My name is " . TextFormat::GOLD . "James" . TextFormat::RED . " and I'm your island villager!");
        //TODO ADD BUTTONS FOR SHOP AND STUFF
        parent::initEntity($nbt);
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $this->num++;
        if ($this->num > 3*20){
            $this->num = 0;
            $e = $this->getWorld()->getNearestEntity($this->getPosition(), 4);
            if ($e !== null && $e instanceof Player){
                $this->lookAt($e->getEyePos());
                //TODO PLAY ANIMATION
            }
        }
        return true;
    }

    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
   //     $nbt->setString(self::ISLAND_ID, $this->getIslandId());
        return $nbt;
    }
    public function setIslandId(string $id){
        $this->islandId = $id;
    }

    public function getIslandId(): string {
        return $this->islandId;
    }

    public function getIsland(): ?Island{
        return IslandManager::getIsland($this->islandId);
    }

    public function flagForDespawn(): void
    {
    }
}