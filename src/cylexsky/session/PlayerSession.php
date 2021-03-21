<?php
declare(strict_types=1);

namespace cylexsky\session;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\players\objects\PlayerObject;
use cylexsky\session\database\PlayerSessionDatabaseHandler;
use cylexsky\session\modules\Level;
use cylexsky\session\modules\Money;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerSession{

    private $object;
    private $island;
    private $levelModule;

    private $moneyModule;

    public function __construct(PlayerObject $object, ?string $island, string $levelData, string $moneyData)
    {
        $this->object = $object;
        $this->island = $island;
        $this->initModules($moneyData, $levelData);
    }

    public function getPlayer(): Player{
        return Server::getInstance()->getPlayerExact($this->getObject()->getUsername());
    }

    private function initModules(string $moneyData, string $levelData){
        $this->moneyModule = new Money($moneyData, $this);
        $this->levelModule = new Level($levelData, $this);
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
        DatabaseManager::emptyQuery("UPDATE player_sessions SET username=?, island=?, level=?, money=? WHERE xuid=?", Query::SERVER_DB, [
            $this->getObject()->getUsername(),
            ($this->getIsland() === null) ? PlayerSessionDatabaseHandler::NULL_STRING : $this->getIsland(),
            $this->getLevelModule()->save(),
            $this->getMoneyModule()->save(),
            $this->getXuid()
        ]);
    }
}