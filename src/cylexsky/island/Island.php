<?php
declare(strict_types=1);

namespace cylexsky\island;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\data\formatter\JsonFormatter;
use core\main\text\TextFormat;
use cylexsky\island\modules\LevelModule;
use cylexsky\island\modules\Members;
use cylexsky\island\modules\PermissionModule;
use cylexsky\island\modules\SettingsModule;
use cylexsky\island\modules\TeleportsModule;
use cylexsky\island\modules\TrustedModule;
use cylexsky\island\modules\TutorialModule;
use cylexsky\island\modules\UpgradesModule;
use cylexsky\island\modules\WealthModule;
use cylexsky\misc\scoreboards\ScoreboardHandler;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;
use cylexsky\worlds\worlds\MainWorld;
use pocketmine\entity\Location;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;

class Island{
    use JsonFormatter;
    private $id;
    private $worldName;
    private $owner;
    private $ownerName;
    private $prestigeShards;

    private $memberModule;
    private $tutorialModule;
    private $permissionsModule;
    private $settingsModule;
    private $wealthModule;
    private $trustedModule;
    private $teleportModule;
    private $levelModule;
    private $upgradeModule;
    private $prestigePoints;

    private $modules;

    private $spawnLocation;

    private $hasBeenChanged;

    public function __construct(string $id, string $world, string $owner, string $ownerName, string $memberData, string $tutorialData, string $permissioNData, string $settingsData, int $wealth, string $trustedData, string $teleportData, int $prestigeShards, string $levelData, string $upgradeData, int $prestigePoints)
    {
        $this->id = $id;
        $this->worldName = $world;
        $this->owner = $owner;
        $this->ownerName = $ownerName;
        $this->hasBeenChanged = false;
        $this->prestigeShards = $prestigeShards;
        $this->prestigePoints = $prestigePoints;
        $this->initModules($memberData, $tutorialData, $permissioNData, $settingsData, $wealth, $trustedData, $teleportData, $levelData, $upgradeData);
    }

    public function initModules(string $memberData, string $tutorialData, string $permissionData, string $settingsData, int $wealth, string $trustedData, string $teleportData, string $levelData, string $upgradeData){
        $this->memberModule = new Members($memberData, $this);
        $this->tutorialModule = new TutorialModule($tutorialData, $this);
        $this->permissionsModule = new PermissionModule($permissionData, $this);
        $this->settingsModule = new SettingsModule($settingsData, $this);
        $this->wealthModule = new WealthModule($wealth, $this);
        $this->trustedModule = new TrustedModule($trustedData, $this);
        $this->teleportModule = new TeleportsModule($teleportData, $this);
        $this->levelModule = new LevelModule($levelData, $this);
        $this->upgradeModule = new UpgradesModule($upgradeData, $this);
    }

    public function setTeleportsIntialLocation(Location $location){
        $this->getTeleportModule()->setTrustedSpawn($location);
    }

    public function reset(string $id){
        $this->loadWorld();
        foreach ($this->getWorld()->getPlayers() as $player){
            MainWorld::teleport($player);
        }
        $this->setHasBeenChanged();
        $this->id = $id;
        $this->worldName = $id;
        $this->wealthModule = new WealthModule(0, $this);
        $this->teleportModule = new TeleportsModule($this->encodeJson(TeleportsModule::getBaseData()), $this);
        $this->levelModule = new LevelModule($this->encodeJson(LevelModule::getBaseData()), $this);
        $this->upgradeModule = new UpgradesModule($this->encodeJson(UpgradesModule::getBaseData()), $this);
        $this->prestigePoints = 0;
        $this->prestigeShards = 0;
    }

    public function getSpawnLocation(): ?Location{
        return $this->spawnLocation;
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
        $player->setGamemode(GameMode::SURVIVAL());
        Server::getInstance()->getWorldManager()->loadWorld($this->getId());
        ScoreboardHandler::sendIslandScoreboard($player);
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

    public function getWealthModule(): WealthModule{
        return $this->wealthModule;
    }

    public function getTrustedModule(): TrustedModule{
        return $this->trustedModule;
    }

    public function getTeleportModule(): TeleportsModule{
        return $this->teleportModule;
    }

    public function getLevelModule(): LevelModule{
        return $this->levelModule;
    }

    public function getUpgradeModule(): UpgradesModule{
        return $this->upgradeModule;
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
        return Server::getInstance()->getWorldManager()->getWorldByName($this->getId());
    }

    public function loadWorld(){
        Server::getInstance()->getWorldManager()->loadWorld($this->getId());
    }

    public function getPrestigeShards(): int {
        return $this->prestigeShards;
    }

    public function addPrestigeShards(int $amount){
        $this->prestigeShards += $amount;
    }

    public function subtractPrestigeShards(int $amount){
        $this->prestigeShards -= abs($amount);
    }

    public function addPrestigePoints(int $amount){
        $this->prestigePoints += $amount;
    }

    public function subtractPrestigePoints(int $amount){
        $this->prestigePoints -= abs($amount);
    }

    public function getPrestigePoints(): int {
        return $this->prestigePoints;
    }

    public function transferOwnership(Player $player){
        $name = $player->getName();
        foreach ($this->getMembersModule()->getOnlineMembers() as $member){
            $session = SessionManager::getSession($member->getXuid());
            $session->sendGoodNotification(TextFormat::GOLD . $this->getOwnerName() . TextFormat::GREEN . " has transferred island ownership to " . TextFormat::GOLD . $name);
        }
        $x = $this->getOwner();
        $n = $this->getOwnerName();
        if (isset(IslandManager::$islandOwnersToIslandID[$n])){
            unset(IslandManager::$islandOwnersToIslandID[$n]);
        }
        MainWorld::teleport(Server::getInstance()->getPlayerExact($this->ownerName));
        $this->getMembersModule()->removeE($player->getXuid());
        $this->owner = $player->getXuid();
        $this->ownerName = $player->getName();
        IslandManager::$islandOwnersToIslandID[$player->getName()] = $this->getId();
        $this->getMembersModule()->addMember(SessionManager::getSession($x), $n, $x, false);
    }

    public function save(){
        $this->hasBeenChanged = false;
       DatabaseManager::emptyQuery("UPDATE islands SET id=?, world=?, owner=?, ownerName=?, memberData=?, tutorialData=?, permissionData=?, settingsData=?, wealth=?, trustedData=?, teleportData=?, prestigeShards=?, levelData=?, upgradeData=?, prestigePoints=? WHERE id=?", Query::SERVER_DB, [
           $this->getId(),
           $this->getWorldName(),
           $this->getOwner(),
           $this->getOwnerName(),
           $this->getMembersModule()->save(),
           $this->getTutorialModule()->save(),
           $this->getPermissionModule()->save(),
           $this->getSettingsModule()->save(),
           $this->getWealthModule()->save(),
           $this->getTrustedModule()->save(),
           $this->getTeleportModule()->save(),
           $this->getPrestigeShards(),
           $this->getLevelModule()->save(),
           $this->getUpgradeModule()->save(),
           $this->getPrestigePoints(),
           $this->getId()
        ]);
    }
}