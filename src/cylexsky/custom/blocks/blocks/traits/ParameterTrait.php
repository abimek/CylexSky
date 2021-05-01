<?php
declare(strict_types=1);

namespace cylexsky\custom\blocks\blocks\traits;

trait ParameterTrait{

    private static $parameters;

    public static function setParameters(array $parameters)
    {
        self::$parameters = $parameters;
    }
    public static function getParameters(): ?array {return self::$parameters;}
}