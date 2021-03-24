<?php
declare(strict_types=1);

namespace cylexsky\island;

use core\database\DatabaseManager;
use core\database\objects\Query;
use cylexsky\island\modules\Members;
use cylexsky\island\modules\TutorialModule;
use cylexsky\session\PlayerSession;
use pocketmine\Server;
use pocketmine\world\World;

class Island{

    private $id;
    private $worldName;
    private $owner;
    private $ownerName;

    private $memberModule;
    private $tutorialModule;

    public function __construct(string $id, string $world, string $owner, string $ownerName, string $memberData, string $tutorialData)
    {
        $this->id = $id;
        $this->worldName = $world;
        $this->owner = $owner;
        $this->ownerName = $ownerName;
        $this->initModules($memberData, $tutorialData);
    }

    public function initModules(string $memberData, string $tutorialData){
        $this->memberModule = new Members($memberData, $this);
        $this->tutorialModule = new TutorialModule($tutorialData, $this);
    }

    public function ownerJoin(PlayerSession $session){
        $this->getTutorialModule()->join($session);
    }

    public function getMembersModule(): Members{
        return $this->memberModule;
    }

    public function getTutorialModule(): TutorialModule{
        return $this->tutorialModule;
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
        DatabaseManager::emptyQuery("UPDATE islands SET world=? owner=?, ownerName=?, memberData=?, tutorialData WHERE id=?", Query::SERVER_DB, [
            $this->getWorldName(),
            $this->getOwner(),
            $this->getWorldName(),
            $this->getMembersModule()->save(),
            $this->getTutorialModule()->save(),
            $this->getId()
        ]);
    }
}