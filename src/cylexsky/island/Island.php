<?php
declare(strict_types=1);

namespace cylexsky\island;

use core\database\DatabaseManager;
use core\database\objects\Query;
use cylexsky\island\modules\Members;
use cylexsky\island\modules\PermissionModule;
use cylexsky\island\modules\SettingsModule;
use cylexsky\island\modules\TutorialModule;
use cylexsky\session\PlayerSession;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;

class Island{

    private $id;
    private $worldName;
    private $owner;
    private $ownerName;

    private $memberModule;
    private $tutorialModule;
    private $permissionsModule;
    private $settingsModule;

    private $hasBeenChanged;

    public function __construct(string $id, string $world, string $owner, string $ownerName, string $memberData, string $tutorialData, string $permissioNData, string $settingsData)
    {
        $this->id = $id;
        $this->worldName = $world;
        $this->owner = $owner;
        $this->ownerName = $ownerName;
        $this->hasBeenChanged = false;
        $this->initModules($memberData, $tutorialData, $permissioNData, $settingsData);
    }

    public function initModules(string $memberData, string $tutorialData, string $permissionData, string $settingsData){
        $this->memberModule = new Members($memberData, $this);
        $this->tutorialModule = new TutorialModule($tutorialData, $this);
        $this->permissionsModule = new PermissionModule($permissionData, $this);
        $this->settingsModule = new SettingsModule($settingsData, $this);
    }

    public function setHasBeenChanged(): void {
        $this->hasBeenChanged = true;
    }

    public function hasBeenChanged(): bool {
        return $this->hasBeenChanged;
    }

    public function ownerJoin(PlayerSession $session){
        $this->ownerName = $session->getPlayer()->getName();
        $this->getTutorialModule()->join($session);
    }

    public function teleportPlayer(Player $player){
        Server::getInstance()->getWorldManager()->loadWorld($this->getId());
        $player->teleport($this->getWorld()->getSpawnLocation());
    }

    public function getMembersModule(): Members{
        return $this->memberModule;
    }

    public function getTutorialModule(): TutorialModule{
        return $this->tutorialModule;
    }

    public function getPermissionModule(): PermissionModule{
        return $this->permissionsModule;
    }

    public function getSettingsModule(): SettingsModule{
        return $this->settingsModule;
    }

    public function getOwner(): string {
        return $this->owner;
    }

    public function getOwnerName(): string {
        return $this->ownerName;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getWorldName(): string {
        return $this->worldName;
    }

    public function getWorld(): ?World{
        return Server::getInstance()->getWorldManager()->getWorldByName($this->getWorldName());
    }

    public function save(){
        $this->hasBeenChanged = false;
        DatabaseManager::emptyQuery("UPDATE islands SET world=? owner=?, ownerName=?, memberData=?, tutorialData=?, permissionData=? settingsData=? WHERE id=?", Query::SERVER_DB, [
            $this->getWorldName(),
            $this->getOwner(),
            $this->getWorldName(),
            $this->getMembersModule()->save(),
            $this->getTutorialModule()->save(),
            $this->getSettingsModule()->save(),
            $this->getPermissionModule()->save(),
            $this->getId()
        ]);
    }
}