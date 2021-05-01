<?php
declare(strict_types=1);

namespace cylexsky\custom\blocks;

use cylexsky\custom\blocks\behavior\StairChair;
use cylexsky\custom\blocks\reader\FileReaderAndRegisterer;
use cylexsky\CylexSky;

class BlockHandler{

    public static function init(){
        self::readAndRegister();
    }

    public static function readAndRegister(){
        new StairChair();
        $cylex = CylexSky::getInstance();
        $files = scandir($cylex->getDataFolder() . "custom/blocks");
        foreach ($files as $file){
            if(!file_exists($cylex->getDataFolder() . "custom/blocks/$file")){
                continue;
            }
            if ($file === "." || $file === ".."){
                continue;
            }
            FileReaderAndRegisterer::read($cylex->getDataFolder() . "custom/blocks/$file");
        }
    }
}