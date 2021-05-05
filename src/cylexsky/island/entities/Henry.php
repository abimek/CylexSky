<?php
declare(strict_types=1);

namespace cylexsky\island\entities;

use core\forms\entity\Button;
use core\forms\entity\EntityFormTrait;
use core\main\text\TextFormat;
use cylexsky\island\Island;
use cylexsky\island\IslandManager;
use cylexsky\session\SessionManager;
use cylexsky\ui\island\IslandUIHandler;
use cylexsky\ui\player\PlayerUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class Henry extends Human{

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
        $this->initEntityForm(Glyphs::BUBBLE_MESSAGE . TextFormat::BOLD_GOLD . " Henry");
        $this->setContent(Glyphs::BUBBLE_MESSAGE . TextFormat::RED . " Welcome back to your island on " . TextFormat::BOLD_GOLD . "Cylex" . TextFormat::AQUA . "Sky!" . TextFormat::RESET_RED . " And always remember im here for you when you need me, now go get shoping!" . Glyphs::SMILE_EMOJI);
        $this->addButton(new Button(Glyphs::GOLD_COIN . TextFormat::GOLD .  " Shop"));
        $this->addButton(new Button(Glyphs::CROWN . TextFormat::GOLD . " Island UI"));
        $this->addButton(new Button(Glyphs::BOX_EXCLAMATION . TextFormat::GRAY . "Island Info"));
        parent::initEntity($nbt);
    }

    public function onButtonInteract(Player $player, Button $button, int $data)
    {
        $session = SessionManager::getSession($player->getXuid());
        if ($session === null || $session->getIsland() === null){
            return;
        }
        switch ($data){
            case 0:
                PlayerUIHandler::sendShopUI($session);
                return;
            case 1:
                if ( $session->getIsland() === null){
                    IslandUIHandler::sendWithoutIsland($session);
                    return;
                }else{
                    IslandUIHandler::sendIslandUI($session);
                }
                return;
            case 2:
                if ($session->getIsland() === null) {
                    $session->sendNotification("You're not in an island!");
                    return;
                }
                IslandUIHandler::sendIslandInfoForm($session);
                return;
        }
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