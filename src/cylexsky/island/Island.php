<?php
declare(strict_types=1);

namespace cylexsky\island;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\text\TextFormat;
use cylexsky\island\modules\LevelModule;
use cylexsky\island\modules\Members;
use cylexsky\island\modules\PermissionModule;
use cylexsky\island\modules\SettingsModule;
use cylexsky\island\modules\TeleportsModule;
use cylexsky\island\modules\TrustedModule;
use cylexsky\island\modules\TutorialModule;
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

    private $spawnLocation;

    private $hasBeenChanged;

    public function __construct(string $id, string $world, string $owner, string $ownerName, string $memberData, string $tutorialData, string $permissioNData, string $settingsData, int $wealth, string $trustedData, string $teleportData, int $prestigeShards, string $levelData)
    {
        $this->id = $id;
        $this->worldName = $world;
        $this->owner = $owner;
        $this->ownerName = $ownerName;
        $this->hasBeenChanged = false;
        $this->prestigeShards = $prestigeShards;
        $this->initModules($memberData, $tutorialData, $permissioNData, $settingsData, $wealth, $trustedData, $teleportData, $levelData);
    }

    public function initModules(string $memberData, string $tutorialData, string $permissionData, string $settingsData, int $wealth, string $trustedData, string $teleportData, string $levelData){
        $this->memberModule = new Members($memberData, $this);
        $this->tutorialModule = new TutorialModule($tutorialData, $this);
        $this->permissionsModule = new PermissionModule($permissionData, $this);
        $this->settingsModule = new SettingsModule($settingsData, $this);
        $this->wealthModule = new WealthModule($wealth, $this);
        $this->trustedModule = new TrustedModule($trustedData, $this);
        $this->teleportModule = new TeleportsModule($teleportData, $this);
        $this->levelModule = new LevelModule($levelData, $this);
    }

    public function setTeleportsIntialLocation(Location $location){
        $this->getTeleportModule()->setTrustedSpawn($location);
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

    public function getPrestigeShards(): int {
        return $this->prestigeShards;
    }

    public function addPrestigeShards(int $amount){
        $this->prestigeShards += $amount;
    }

    public function subtractPrestigeShards(int $amount){
        $this->prestigeShards -= abs($amount);
    }

    public function transferOwnership(Player $player){
        $name = $player->getName();
        foreach ($this->getMembersModule()->getOnlineMembers() as $member){
            $session = SessionManager::getSession($member->getXuid());
            $session->sendGoodNotification(TextFormat::GOLD . $this->getOwnerName() . TextFormat::GREEN . " has transferred island ownership to " . TextFormat::GOLD . $name);
        }
        $x = $this->getOwner();
        $n = $this->getOwnerName();
        MainWorld::teleport(Server::getInstance()->getPlayerExact($this->ownerName));
        $this->owner = $player->getXuid();
        $this->ownerName = $player->getName();
        $this->getMembersModule()->addMember(SessionManager::getSession($x), $n, $x);
    }

    public function save(){
        $this->hasBeenChanged = false;
       DatabaseManager::emptyQuery("UPDATE islands SET world=?, owner=?, ownerName=?, memberData=?, tutorialData=?, permissionData=?, settingsData=?, wealth=?, trustedData=?, teleportData=?, prestigeShards=?, levelData=? WHERE id=?", Query::SERVER_DB, [
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
            $this->getId()
        ]);
    }
}