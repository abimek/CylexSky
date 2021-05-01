<?php
declare(strict_types=1);

namespace cylexsky\misc\shop;

use cylexsky\CylexSky;
use cylexsky\misc\shop\objects\Category;
use pocketmine\utils\Config;

class ShopHandler{

    public static $categories = [];

    public function __construct()
    {
        $this->loadShops();
    }

    private function loadShops(){
        $dataFolder = CylexSky::getInstance()->getDataFolder();
        $categoryFiles = $dataFolder . "shopcatas.json";
        $config = new Config($categoryFiles, Config::JSON);
        foreach ($config->getAll() as $file => $value){
            self::registerCategory(new Category($value["name"], $value["texture"], $value["buttonName"], new Config($dataFolder . "/shop/$file.json", Config::JSON)));
        }
    }

    public static function getCategories(){
        return self::$categories;
    }

    public static function registerCategory(Category $category){
        self::$categories[$category->getName()] = $category;
    }
}