<?php
declare(strict_types=1);

namespace cylexsky\custom\items\items\traits;

trait CustomItemTrait{

    private static $parameters;
    private static $initData = [];
    private static $aProperties = [];
    private static $aComponents = [];



    public static function setParameters(array $parameters)
    {
        self::$parameters = $parameters;
    }

    public static function setInitData(array $data){
        self::$initData = $data;
    }

    public static function setCustomProperties(array $properties){
        self::$aProperties = $properties;
    }

    public static function setCustomComponents(array $comps){
        self::$aComponents = $comps;
    }

    public static function getParameters(): ?array {return self::$parameters;}
    public static function getInitData(): array {return self::$initData;}
    public static function getAProperties(): array {return self::$aProperties;}
    public static function getAComponents(): array {return self::$aComponents;}
}