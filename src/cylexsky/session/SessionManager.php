<?php
declare(strict_types=1);

namespace cylexsky\session;

use core\main\managers\Manager;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use cylexsky\session\database\PlayerSessionDatabaseHandler;

class SessionManager extends Manager{

    private static $sessions = [];

    protected function init(): void
    {
        PlayerSessionDatabaseHandler::init();
        $this->initCreationDeletionCallables();
    }

    private function initCreationDeletionCallables(){

        PlayerManager::addPlayerSessionCreationCallable(function (\core\players\session\PlayerSession $object){
            $object = $object->getObject();
            PlayerSessionDatabaseHandler::createSession($object);
        });
        PlayerManager::addPlayerSessionDestructionCallable(function (\core\players\session\PlayerSession $object){
            $object = $object->getObject();
            PlayerSessionDatabaseHandler::deleteSession($object);
        });
    }

    public static function createSession(PlayerSession $session){
        if (!self::sessionExists($session->getXuid())){
            self::$sessions[$session->getXuid()] = $session;
        }
    }

    public static function getSession(string $xuid): ?PlayerSession{
        return self::sessionExists($xuid) ? self::$sessions[$xuid] : null;
    }

    public static function sessionExists(string $xuid){
        return isset(self::$sessions[$xuid]);
    }

    public static function removeSession(PlayerObject $object){
        if (self::sessionExists($object->getXuid())){
            unset(self::$sessions[$object->getXuid()]);
        }
    }

    protected function close(): void
    {
        foreach (self::$sessions as $session){
            $session->save();
        }
    }
}