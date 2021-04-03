<?php
declare(strict_types=1);

namespace cylexsky\session\database;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\data\formatter\JsonFormatter;
use core\players\objects\PlayerObject;
use cylexsky\CylexSky;
use cylexsky\session\modules\Level;
use cylexsky\session\modules\Money;
use cylexsky\session\modules\Stats;
use cylexsky\session\modules\Toggles;
use cylexsky\session\modules\Trusted;
use cylexsky\session\PlayerSession;
use cylexsky\session\SessionManager;

final class PlayerSessionDatabaseHandler{

    use JsonFormatter;

    public const NULL_STRING = "\-/+-76fuwWugmJ76os,#909^%";

    public static function init(){
        if (CylexSky::getInstance()->shouldReset()){
            DatabaseManager::emptyQuery("DROP TABLE player_sessions");
        }
        DatabaseManager::emptyQuery("CREATE TABLE IF NOT EXISTS player_sessions(xuid VARCHAR(36) PRIMARY KEY, username TEXT, island TEXT, money TEXT, toggles TEXT, stats TEXT, trusted TEXT, level TEXT)");
    }

    public static function callableOfflineXuid(string $xuid, callable $callable){
        if (SessionManager::getSession($xuid) !== null){
            $callable(SessionManager::getSession($xuid));
            return;
        }
        $query = new Query("SELECT * FROM player_sessions WHERE xuid=?", [$xuid], function ($result)use($callable){
            foreach ($result as $data){
                $session = new PlayerSession(null, $data["xuid"], $data["island"], $data["level"], $data["money"], $data["toggles"], $data["stats"], $data["trusted"]);
                $callable($session);
                $session->saveOffline();
            }
        });
        DatabaseManager::query($query);
    }

    public static function createSession(PlayerObject $object){
        $xuid = $object->getXuid();
        if (SessionManager::sessionExists($xuid)){
            return;
        }
        $query = new Query("SELECT * FROM player_sessions WHERE xuid=?", [
            $xuid
        ], function ($results)use($object, $xuid){
           foreach ($results as $data){
               $session = new PlayerSession($object, $data["xuid"], $data["island"], $data["level"], $data["money"], $data["toggles"], $data["stats"], $data["trusted"]);
               SessionManager::createSession($session);
               return;
           }
           DatabaseManager::emptyQuery("INSERT IGNORE INTO player_sessions(xuid, username, island, level, money, toggles, stats, trusted) VALUES(?, ?, ?, ?, ?, ?, ?, ?);", Query::SERVER_DB, [
               $object->getXuid(),
               $object->getUsername(),
               self::NULL_STRING,
               self::staticEncodeJson(Level::getBaseData()),
               self::staticEncodeJson(Money::getBaseData()),
               self::staticEncodeJson(Toggles::getBaseData()),
               self::staticEncodeJson(Stats::getBaseData()),
               self::staticEncodeJson(Trusted::getBaseData())
           ]);
            $session = new PlayerSession($object, $xuid, self::NULL_STRING, self::staticEncodeJson(Level::getBaseData()), self::staticEncodeJson(Money::getBaseData()), self::staticEncodeJson(Toggles::getBaseData()),  self::staticEncodeJson(Stats::getBaseData()), self::staticEncodeJson(Trusted::getBaseData()));
            SessionManager::createSession($session);
        });
        DatabaseManager::query($query);
    }

    public static function deleteSession(PlayerObject $object){
        $xuid = $object->getXuid();
        if (!SessionManager::sessionExists($xuid)) {
            return;
        }
        $session = SessionManager::getSession($xuid);
        if ($session === null){
            return;
        }
        $session->save();
        SessionManager::removeSession($object);
    }
}