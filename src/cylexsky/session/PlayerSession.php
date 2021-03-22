<?php
declare(strict_types=1);

namespace cylexsky\session;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\players\objects\PlayerObject;
use cylexsky\session\database\PlayerSessionDatabaseHandler;
use cylexsky\session\modules\Level;
use cylexsky\session\modules\Money;
use cylexsky\session\modules\Stats;
use cylexsky\session\modules\Toggles;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerSession{

    private $object;
    private $island;

    private $levelModule;
    private $moneyModule;
    private $toggleModule;
    private $statsModule;

    public function __construct(PlayerObject $object, ?string $island, string $levelData, string $moneyData, string $toggleData, string $statsData)
    {
        $this->object = $object;
        $this->island = $island;
        $this->initModules($moneyData, $levelData, $toggleData, $statsData);
    }

    public function getPlayer(): Player{
        return Server::getInstance()->getPlayerExact($this->getObject()->getUsername());
    }

    private function initModules(string $moneyData, string $levelData, string $toggleData, string $statsData){
        $this->moneyModule = new Money($moneyData, $this);
        $this->levelModule = new Level($levelData, $this);
        $this->toggleModule = new Toggles($toggleData, $this);
        $this->statsModule = new Stats($statsData, $this);
    }

    /**
     * @return mixed
     */
    public function getMoneyModule(): Money
    {
        return $this->moneyModule;
    }

    /**
     * @return Level
     */
    public function getLevelModule(): Level
    {
        return $this->levelModule;
    }

    /**
     * @return Toggles
     */
    public function getTogglesModule(): Toggles{
        return $this->toggleModule;
    }

    /**
     * @return Stats
     */
    public function getStatsModule(): Stats{
        return $this->statsModule;
    }

    /**
     * @return mixed
     */
    public function getXuid()
    {
        return $this->object->getXuid();
    }


    /**
     * @return PlayerObject
     */
    public function getObject(): PlayerObject
    {
        return $this->object;
    }

    /**
     * @return string|null
     */
    public function getIsland(): ?string
    {
        return $this->island;
    }

    public function save(){
        DatabaseManager::emptyQuery("UPDATE player_sessions SET username=?, island=?, level=?, money=?, toggles=?, stats=? WHERE xuid=?", Query::SERVER_DB, [
            $this->getObject()->getUsername(),
            ($this->getIsland() === null) ? PlayerSessionDatabaseHandler::NULL_STRING : $this->getIsland(),
            $this->getLevelModule()->save(),
            $this->getMoneyModule()->save(),
            $this->getTogglesModule()->save(),
            $this->getStatsModule()->save(),
            $this->getXuid()
        ]);
    }
}