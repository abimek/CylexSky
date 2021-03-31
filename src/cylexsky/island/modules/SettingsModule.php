<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

class SettingsModule extends BaseModule{

    public const PVP = 0;
    public const VISITING = 1;
    public const EXPLOSIONS = 2;

    private $settings;

    public function init(array $data)
    {
        $this->settings = $data;
    }

    public function getSetting(int $setting): ?bool {
        if (isset($this->settings[$setting])){
            if ($setting === self::VISITING && $this->getSetting(self::PVP) === true){
                return false;
            }
            return $this->settings[$setting];
        }
        return null;
    }

    public function setSettings(array $settings): void {
        $this->getIsland()->hasBeenChanged();
        foreach ($settings as $k => $v){
            $this->setSetting($k, $v);
        }
    }

    public function setSetting(int $setting, bool $value): void {
        if ($setting === self::PVP && $value === true){
            $this->setSetting(self::VISITING, false);
        }
        if ($setting === self::VISITING && $value === true && $this->getSetting(self::PVP) === true){
            $this->setSetting(self::PVP, false);
        }
        $this->getIsland()->hasBeenChanged();
        $this->settings[$setting] = $value;
    }

    public static function getBaseData(): array
    {
        return [self::PVP => false, self::VISITING => false, self::EXPLOSIONS => false];
    }

    public function save()
    {
        return json_encode($this->settings);
    }
}