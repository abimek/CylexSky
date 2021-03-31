<?php
declare(strict_types=1);

namespace cylexsky\island\creation;

use cylexsky\utils\RankIds;
use pocketmine\entity\Location;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use Ramsey\Uuid\Uuid;

abstract class BaseIsland implements RankIds{

    private $id;
    private $world;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createWorld();
    }

    abstract function getPresetName(): string;

    public function getId(): string {
        return $this->id;
    }

    public function createWorld(){
        $name = Server::getInstance()->getDataPath() . "worlds" . "/" . $this->getId() . "/";
        $this->full_copy(Server::getInstance()->getDataPath() . "worlds" . "/" . $this->getPresetName() . "/", $name);
        Server::getInstance()->getWorldManager()->loadWorld($this->id);
        $this->world = Server::getInstance()->getWorldManager()->getWorldByName($this->id);
        $this->world->setSpawnLocation($this->getPosition());
    }

    public function getPosition(): Position{
        return new Position(0, 0, 0, $this->world);
    }

    public function getLocation(): Location{
        return new Location(0, 0, 0, 0, 0, $this->world);
    }

    public function getJerryLocation(): Location{
        return new Location(0, 0, 0, 0, 0, $this->world);
    }

    public function getRequiredRank(): int {
        return self::ROOKIE;
    }

    public function getWorld(): World{
        return $this->world;
    }

    private function full_copy( $source, $target ) {
        if ( is_dir( $source ) ) {
            @mkdir( $target );
            $d = dir( $source );
            while ( FALSE !== ( $entry = $d->read() ) ) {
                if ( $entry == '.' || $entry == '..' ) {
                    continue;
                }
                $Entry = $source . '/' . $entry;
                if ( is_dir( $Entry ) ) {
                    $this->full_copy( $Entry, $target . '/' . $entry );
                    continue;
                }
                copy( $Entry, $target . '/' . $entry );
            }

            $d->close();
        }else {
            copy( $source, $target );
        }
    }

}