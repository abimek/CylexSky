<?php
declare(strict_types=1);

namespace cylexsky\session;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\text\TextFormat;
use core\players\objects\PlayerObject;
use cylexsky\island\Island;
use cylexsky\island\IslandManager;
use cylexsky\session\database\PlayerSessionDatabaseHandler;
use cylexsky\session\modules\Level;
use cylexsky\session\modules\Money;
use cylexsky\session\modules\RequestModule;
use cylexsky\session\modules\Stats;
use cylexsky\session\modules\Teleport;
use cylexsky\session\modules\Toggles;
use cylexsky\session\modules\Trusted;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerSession{

    public const SEND_COMMAND_NOTIFICATION_COLOR = TextFormat::RED;
    public const NOTIFICATION_COLOR = TextFormat::GRAY;
    public const GOOD_NOTIFICATION_COLOR = TextFormat::GREEN;

    private $object;
    private $island;
    private $xuid;

    private $levelModule;
    private $moneyModule;
    private $toggleModule;
    private $statsModule;
    private $requestModule;
    private $teleportModule;
    private $trustedModule;

    public function __construct(?PlayerObject $object, string $xuid, ?string $island, string $levelData, string $moneyData, string $toggleData, string $statsData, string $trustedData)
    {
        $this->xuid = $xuid;
        $this->object = $object;
        if ($island !== PlayerSessionDatabaseHandler::NULL_STRING){
            $this->island = $island;
        }else{
            $this->island = null;
        }
        $this->initModules($moneyData, $levelData, $toggleData, $statsData, $trustedData);
    }

    public function onJoin(){
        if ($this->getIslandObject() !== null){
            if ($this->getObject()->getUsername() === $this->getIslandObject()->getOwnerName()){
                $this->getIslandObject()->ownerJoin($this);
            }
        }
    }

    public function getPlayer(): Player{
        return Server::getInstance()->getPlayerExact($this->getObject()->getUsername());
    }

    private function initModules(string $moneyData, string $levelData, string $toggleData, string $statsData, string $trustedData){
        $this->moneyModule = new Money($moneyData, $this);
        $this->levelModule = new Level($levelData, $this);
        $this->toggleModule = new Toggles($toggleData, $this);
        $this->statsModule = new Stats($statsData, $this);
        $this->requestModule = new RequestModule($this);
        $this->teleportModule = new Teleport($this);
        $this->trustedModule = new Trusted($trustedData, $this);
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

    public function getRequestModule(): RequestModule{
        return $this->requestModule;
    }

    public function getTeleportModule(): Teleport{
        return $this->teleportModule;
    }

    public function  getTrustedModule(): Trusted{
        return $this->trustedModule;
    }

    /**
     * @return mixed
     */
    public function getXuid()
    {
        return $this->xuid;
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

    /**
     * @param string $id
     */
    public function setIsland(?string $id){
        $this->island = $id;
    }

    /**
     * @return Island|null
     */
    public function getIslandObject(): ?Island{
        if ($this->island === null){
            return null;
        }
        return IslandManager::getIsland($this->getIsland());
    }

    public function sendNotification(string $message){
        $player = $this->getPlayer();
        $player->sendMessage(Glyphs::BOX_EXCLAMATION . self::NOTIFICATION_COLOR . "" . $message);
    }

    public function sendGoodNotification(string $message){
        $player = $this->getPlayer();
        $player->sendMessage(Glyphs::GREEN_BOX_EXCLAMATION . self::GOOD_NOTIFICATION_COLOR. " " . $message);
    }

    public function sendCommandParameters(string $command){
        $player = $this->getPlayer();
        $player->sendMessage(Glyphs::OPEN_BOOK . TextFormat::RED . $command);
    }

    public function sendJerryMessage(string $l1, string $l2, string $l3){
        $player = $this->getPlayer();
        $player->sendMessage(Glyphs::JERRY_LINE_1 . TextFormat::GRAY . $l1);
        $player->sendMessage(Glyphs::JERRY_LINE_2 . TextFormat::GRAY . $l2);
        $player->sendMessage(Glyphs::JERRY_LINE_3 . TextFormat::GRAY . $l3);
    }

    public function sendIslandMessage(string $message){
        $this->sendNotification($message);
    }

    public function sendCommandNotification(string $message){
        $player = $this->getPlayer();
        $player->sendMessage(Glyphs::BOX_EXCLAMATION . self::SEND_COMMAND_NOTIFICATION_COLOR . "" . $message);
    }

    public function save(){
        DatabaseManager::emptyQuery("UPDATE player_sessions SET username=?, island=?, level=?, money=?, toggles=?, stats=?, trusted=? WHERE xuid=?", Query::SERVER_DB, [
            $this->getObject()->getUsername(),
            ($this->getIsland() === null) ? PlayerSessionDatabaseHandler::NULL_STRING : $this->getIsland(),
            $this->getLevelModule()->save(),
            $this->getMoneyModule()->save(),
            $this->getTogglesModule()->save(),
            $this->getStatsModule()->save(),
            $this->getTrustedModule()->save(),
            $this->getXuid()
        ]);
    }

    public function saveOffline(){
        DatabaseManager::emptyQuery("UPDATE player_sessions SET island=?, level=?, money=?, toggles=?, stats=?, trusted=? WHERE xuid=?", Query::SERVER_DB, [
            ($this->getIsland() === null) ? PlayerSessionDatabaseHandler::NULL_STRING : $this->getIsland(),
            $this->getLevelModule()->save(),
            $this->getMoneyModule()->save(),
            $this->getTogglesModule()->save(),
            $this->getStatsModule()->save(),
            $this->getTrustedModule()->save(),
            $this->getXuid()
        ]);
    }
}