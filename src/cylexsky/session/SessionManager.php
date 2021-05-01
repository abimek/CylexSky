<?php
declare(strict_types=1);

namespace cylexsky\session;

use core\main\managers\Manager;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use cylexsky\CylexSky;
use cylexsky\session\database\PlayerSessionDatabaseHandler;
use cylexsky\session\listeners\PlayerSessionListener;
use cylexsky\session\tasks\SaveTask;

class SessionManager extends Manager{

    public const TIME = 20*60*5;

    private static $sessions = [];

    protected function init(): void
    {
        PlayerSessionDatabaseHandler::init();
        $this->initCreationDeletionCallables();
        CylexSky::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new SaveTask(), self::TIME, self::TIME);
        CylexSky::getInstance()->getServer()->getPluginManager()->registerEvents(new PlayerSessionListener(), CylexSky::getInstance());
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

    public static function saveSessions(){
        foreach (self::$sessions as $session){
            if ($session->hasChanged()){
                $session->save();
            }
        }
    }

    protected function close(): void
    {
        foreach (self::$sessions as $session){
            $session->save();
        }
    }
}