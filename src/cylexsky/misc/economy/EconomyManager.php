<?php
declare(strict_types=1);

namespace cylexsky\misc\economy;

use core\main\managers\Manager;
use cylexsky\CylexSky;
use cylexsky\misc\economy\events\money\AddMoneyEvent;
use cylexsky\misc\economy\providers\Provider;
use cylexsky\misc\economy\providers\YamlProvider;
use cylexsky\misc\economy\tasks\SaveTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class EconomyManager extends Manager{

    private $config;

    protected function init(): void
    {
        $this->config = new Config(CylexSky::getInstance()->getDataFolder() . "economy.yml", Config::YAML);
        $this->onLoad();
        $this->onEnable();
    }

    protected function close(): void
    {
        // TODO: Implement close() method.
    }

    public static $prefix = "§b§l[EconomyAPI] §r§7";

    const API_VERSION = 3;
    const PACKAGE_VERSION = "5.7";

    const RET_NO_ACCOUNT = -3;
    const RET_CANCELLED = -2;
    const RET_NOT_FOUND = -1;
    const RET_INVALID = 0;
    const RET_SUCCESS = 1;

    private static $instance = null;

    /**
     * @var Provider
     */
    private $provider;

    public static function getInstance() : EconomyManager{
        return self::$instance;
    }

    public function onLoad(){
        if(self::$instance !== null){
            throw new \InvalidStateException();
        }
        self::$instance = $this;
    }

    public function getConfig(): Config{
        return $this->config;
    }

    public function saveDefaultConfig(){
        $this->getConfig()->save();
    }

    public function onEnable(){
        $this->saveDefaultConfig();
        switch(strtolower($this->getConfig()->get("provider"))){
            case "yaml":
                $this->provider = new YamlProvider($this);
                break;
            default:
                throw new \UnexpectedValueException("Invalid database was given");
        }

        $saveInterval = $this->getConfig()->get("auto-save-interval") * 1200;

        if($saveInterval > 0){
            CylexSky::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new SaveTask($this), $saveInterval, $saveInterval);
        }

        Server::getInstance()->getPluginManager()->registerEvents(new class(CylexSky::getInstance()) implements Listener{
            public function __construct(CylexSky $owner){
                $this->owner = $owner;
            }

            /**
             * @ignoreCancelled true
             *
             * @priority MONITOR
             */
            public function handlePlayerJoin(PlayerJoinEvent $event){
                $player = $event->getPlayer();

                if(!EconomyManager::getInstance()->accountExists($player)){
                    EconomyManager::getInstance()->createAccount($player, false, true);
                }
            }
        }, CylexSky::getInstance());
    }

    public function onDisable(){
        $this->saveAll();

        self::$instance = null;
    }

    public function getMonetaryUnit() : string{
        return $this->getConfig()->get("monetary-unit", "￦");
    }

    public function thousandSeparatedFormat($money) : string{
        return number_format($money) . $this->getMonetaryUnit();
    }

    public function koreanWonFormat($money) : string{
        $str = '';
        $elements = [];
        if($money >= 1000000000000){
            $elements[] = floor($money / 1000000000000) . "조";
            $money %= 1000000000000;
        }
        if($money >= 100000000){
            $elements[] = floor($money / 100000000) . "억";
            $money %= 100000000;
        }
        if($money >= 10000){
            $elements[] = floor($money / 10000) . "만";
            $money %= 10000;
        }
        if(count($elements) == 0 || $money > 0){
            $elements[] = $money;
        }
        return implode(" ", $elements) . $this->getMonetaryUnit();
    }

    public function getAllMoney() : array{
        return $this->provider->getAll();
    }

    public function createAccount($player, $defaultMoney = false, bool $force = false) : bool{
        $player = strtolower($player instanceof Player ? $player->getName() : $player);

        if(!$this->provider->accountExists($player)){
            $defaultMoney = ($defaultMoney === false) ? $this->getConfig()->get("default-money") : $defaultMoney;

            $this->provider->createAccount($player, $defaultMoney);

        }
        return false;
    }

    public function accountExists($player) : bool{
        $player = strtolower($player instanceof Player ? $player->getName() : $player);

        return $this->provider->accountExists($player);
    }

    public function myMoney($player){
        $player = strtolower($player instanceof Player ? $player->getName() : $player);

        return $this->provider->getMoney($player);
    }

    public function setMoney($player, $amount, bool $force = false, string $issuer = "none") : int{
        if($amount < 0){
            return self::RET_INVALID;
        }
        $player = strtolower($player instanceof Player ? $player->getName() : $player);

        if($this->provider->accountExists($player)){
            $amount = round($amount, 2);
            if($amount > $this->getConfig()->get("max-money")){
                return self::RET_INVALID;
            }

            $this->provider->setMoney($player, $amount);
            return self::RET_SUCCESS;
        }
        return self::RET_NO_ACCOUNT;
    }

    public function addMoney($player, $amount, bool $force = false, $issuer = "none") : int{
        if($amount < 0){
            return self::RET_INVALID;
        }
        $player = strtolower($player instanceof Player ? $player->getName() : $player);

        if(($money = $this->provider->getMoney($player)) !== false){
            $amount = round($amount, 2);
            if($money + $amount > $this->getConfig()->get("max-money")){
                return self::RET_INVALID;
            }

            $ev = new AddMoneyEvent($this, $player, $amount, $issuer);
            $ev->call();
            if(!$ev->isCancelled() or $force === true){
                $this->provider->addMoney($player, $amount);
                return self::RET_SUCCESS;
            }
            return self::RET_CANCELLED;
        }
        return self::RET_NO_ACCOUNT;
    }

    public function reduceMoney($player, $amount, bool $force = false, $issuer = "none") : int{
        if($amount < 0){
            return self::RET_INVALID;
        }
        $player = strtolower($player instanceof Player ? $player->getName() : $player);

        if(($money = $this->provider->getMoney($player)) !== false){
            $amount = round($amount, 2);
            if($money - $amount < 0){
                return self::RET_INVALID;
            }

            $this->provider->reduceMoney($player, $amount);
            return self::RET_SUCCESS;
        }
        return self::RET_NO_ACCOUNT;
    }

    public function getRank($player){
        $player = strtolower($player instanceof Player ? $player->getName() : $player);

        return $this->provider->getRank($player);
    }

    public function getPlayerByRank(int $rank){
        return $this->provider->getPlayerByRank($rank);
    }

    public function saveAll(){
        if($this->provider instanceof Provider){
            $this->provider->save();
        }
    }
}