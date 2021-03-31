<?php
declare(strict_types=1);

namespace cylexsky\island\modules;

use cylexsky\session\PlayerSession;

class TutorialModule extends BaseModule{

    public const LAST_TUTORIAL = 9;

    private $inTutorial;
    private $tutorialPhase;

    public function init(array $data)
    {
        $this->inTutorial = $data[0];
        $this->tutorialPhase = $data[1];
    }

    public function inTutorial(): bool {
        return false;
       // return $this->inTutorial;
    }

    public function getTutorialPhase(): int {
        return $this->tutorialPhase;
    }

    public function finishTutorial(){
        //TODO what happens when they finish the tutorial
    }

    public function progress(){
        $this->getIsland()->hasBeenChanged();
        if ($this->tutorialPhase === self::LAST_TUTORIAL){
            $this->inTutorial = false;
            $this->finishTutorial();
        }
        $this->tutorialPhase++;
    }

    public function join(PlayerSession $session){
        if (!$this->inTutorial()){
            return;
        }

        $this->getIsland()->teleportPlayer($session->getPlayer());
        $this->{"join" . $this->tutorialPhase}($session);
    }

    public function join1(PlayerSession $session){
        $session->sendJerryMessage("Welcome to you're island!", "go open up that chest", "and place your resource node!");
    }

    public function join2(PlayerSession $session){

    }

    public static function getBaseData(): array
    {
        return [true, 1];
    }

    public function save()
    {
        return $this->encodeJson([$this->inTutorial, $this->tutorialPhase]);
    }
}