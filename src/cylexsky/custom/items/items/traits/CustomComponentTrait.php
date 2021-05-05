<?php
declare(strict_types=1);

namespace cylexsky\custom\items\items\traits;

trait CustomComponentTrait{

    public function initPropertiesAndInitialData(){
        $this->initComponentTag(self::getInitData()[0], self::getInitData()[1]);
        foreach (self::getAProperties() as $name => $value){
            $this->addProperty($name, $value);
        }
        foreach (self::getAComponents() as $name => $value){
            $this->addComponent($name, $value);
        }
    }
}