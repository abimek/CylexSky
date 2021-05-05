<?php
declare(strict_types=1);

namespace cylexsky\session;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\text\TextFormat;
use core\players\objects\PlayerObject;
use core\ranks\levels\StaffRankLevels;
use core\ranks\Rank;
use core\ranks\RankManager;
use core\ranks\types\RankTypes;
use cylexsky\island\Island;
use cylexsky\island\IslandManager;
use cylexsky\session\database\PlayerSessionDatabaseHandler;
use cylexsky\session\listeners\PlayerSessionListener;
use cylexsky\session\modules\EmojiModule;
use cylexsky\session\modules\Level;
use cylexsky\session\modules\MiscModule;
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

    public const UI_INV = 1;
    public const UI_FORM = 2;

    public const SEND_COMMAND_NOTIFICATION_COLOR = TextFormat::RED;
    public const NOTIFICATION_COLOR = TextFormat::GRAY;
    public const GOOD_NOTIFICATION_COLOR = TextFormat::GREEN;

    private $hasChanged = false;

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
    private $miscModule;
    private $emojisModule;

    private $uiType = self::UI_FORM;

    public function __construct(?PlayerObject $object, string $xuid, ?string $island, string $levelData, string $moneyData, string $toggleData, string $statsData, string $trustedData, string $emojisModule)
    {
        $this->xuid = $xuid;
        $this->object = $object;
        if ($island !== PlayerSessionDatabaseHandler::NULL_STRING){
            $this->island = $island;
        }else{
            $this->island = null;
        }
        $this->initModules($moneyData, $levelData, $toggleData, $statsData, $trustedData, $emojisModule);
    }

    public function onJoin(){
        if ($this->getIslandObject() !== null){
            if ($this->getObject()->getUsername() === $this->getIslandObject()->getOwnerName()){
                $this->getIslandObject()->ownerJoin($this);
            }
        }
        $xuid = $this->getXuid();
        $v = PlayerSessionListener::$deviceIds;
        if (isset($v[$xuid])){
            $this->setUiType($v[$xuid]);
            unset(PlayerSessionListener::$deviceIds[$xuid]);
        }
    }

    public function getUiType(){
        return $this->uiType;
    }

    public function setUiType(int $type){
        $this->uiType = $type;
    }

    public function getPlayer(): Player{
        return Server::getInstance()->getPlayerExact($this->getObject()->getUsername());
    }

    public function hasChanged(): bool {
        return $this->hasChanged;
    }

    public function setHasBeenChanged(){
        $this->hasChanged = true;
    }

    private function initModules(string $moneyData, string $levelData, string $toggleData, string $statsData, string $trustedData, string $emojisModule){
        $this->moneyModule = new Money($moneyData, $this);
        $this->levelModule = new Level($levelData, $this);
        $this->toggleModule = new Toggles($toggleData, $this);
        $this->statsModule = new Stats($statsData, $this);
        $this->requestModule = new RequestModule($this);
        $this->teleportModule = new Teleport($this);
        $this->trustedModule = new Trusted($trustedData, $this);
        $this->miscModule = new MiscModule($this);
        $this->emojisModule = new EmojiModule($emojisModule, $this);
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

    public function getMiscModule(): MiscModule{
        return $this->miscModule;
    }

    public function getEmojisModule(): EmojiModule{
        return $this->emojisModule;
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
        $player->sendMessage(Glyphs::BOX_EXCLAMATION . self::NOTIFICATION_COLOR . " " . $message);
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

    public function sendShopMessage(string $message){
        $this->sendNotification($message);
    }

    public function sendAdminMessage(string $message){
        $this->getPlayer()->sendMessage(TextFormat::BOLD_DARK_GRAY . "[" . TextFormat::RESET_RED . "Admin" . TextFormat::BOLD_DARK_GRAY . "]" . TextFormat::RESET_GRAY . " " . $message);
    }

    public function getRank(): Rank{
        return RankManager::getRank($this->getObject()->getRank());
    }

    public function isServerOperator(){
        $rank = RankManager::getRank($this->getObject()->getRank());
        if ($rank === null){
            return false;
        }
        if ($rank->getType() === RankTypes::STAFF_RANK && $rank->getLevel() >= StaffRankLevels::ADMIN){
            return true;
        }
        return false;
    }

    public function isStaff(): bool {
        return ($this->getRank()->getType() === RankTypes::STAFF_RANK);
    }

    public function sendIslandMessage(string $message){
        $this->sendNotification($message);
    }

    public function sendCommandNotification(string $message){
        $player = $this->getPlayer();
        $player->sendMessage(Glyphs::BOX_EXCLAMATION . self::SEND_COMMAND_NOTIFICATION_COLOR . "" . $message);
    }

    public function save(){
        $this->hasChanged = false;
        DatabaseManager::emptyQuery("UPDATE player_sessions SET username=?, island=?, level=?, money=?, toggles=?, stats=?, trusted=?, emojis=? WHERE xuid=?", Query::SERVER_DB, [
            $this->getObject()->getUsername(),
            ($this->getIsland() === null) ? PlayerSessionDatabaseHandler::NULL_STRING : $this->getIsland(),
            $this->getLevelModule()->save(),
            $this->getMoneyModule()->save(),
            $this->getTogglesModule()->save(),
            $this->getStatsModule()->save(),
            $this->getTrustedModule()->save(),
            $this->getEmojisModule()->save(),
            $this->getXuid()
        ]);
    }

    public function saveOffline(){
        $this->hasChanged = false;
        DatabaseManager::emptyQuery("UPDATE player_sessions SET island=?, level=?, money=?, toggles=?, stats=?, trusted=? emojis=? WHERE xuid=?", Query::SERVER_DB, [
            ($this->getIsland() === null) ? PlayerSessionDatabaseHandler::NULL_STRING : $this->getIsland(),
            $this->getLevelModule()->save(),
            $this->getMoneyModule()->save(),
            $this->getTogglesModule()->save(),
            $this->getStatsModule()->save(),
            $this->getTrustedModule()->save(),
            $this->getEmojisModule()->save(),
            $this->getXuid()
        ]);
    }
}