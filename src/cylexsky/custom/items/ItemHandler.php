<?php
declare(strict_types=1);

namespace cylexsky\custom\items;

use cylexsky\custom\items\reader\ItemReaderAndRegisterer;
use cylexsky\custom\items\reader\ToolTierReader;
use cylexsky\CylexSky;

class ItemHandler{

    public static function init(){
        self::readAndRegister();
    }

    public static function readAndRegister(){
        $cylex = CylexSky::getInstance();
        ToolTierReader::readToolTiers($cylex->getDataFolder() . "custom/tooltiers.json");
        $files = scandir($cylex->getDataFolder() . "custom/items");
        foreach ($files as $file){
            if(!file_exists($cylex->getDataFolder() . "custom/items/$file")){
                continue;
            }
            if ($file === "." || $file === ".."){
                continue;
            }
            ItemReaderAndRegisterer::read($cylex->getDataFolder() . "custom/items/$file");
        }
    }
}