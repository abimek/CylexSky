<?php
namespace cylexsky\custom\blocks\behavior;

use cylexsky\custom\blocks\behavior\listener\ChairListener;
use cylexsky\custom\blocks\blocks\Chair;
use cylexsky\CylexSky;
use pocketmine\block\Block;
use pocketmine\entity\Zombie;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\utils\Config;

class StairChair{

    public $sit = [];
    public $config;

    public function __construct(){
        $plugin = CylexSky::getInstance();
        Server::getInstance()->getPluginManager()->registerEvents(new ChairListener($this), CylexSky::getInstance());
        @mkdir($plugin->getDataFolder(), 0744, true);
        $plugin->saveResource('chairsettings.yml', false);
        $this->config = new Config(CylexSky::getInstance()->getDataFolder().'chairsettings.yml', Config::YAML);
    }

    public function isStairBlock(Block $block) : bool{
        $v = $block instanceof Chair;
        return $v;
    }

    public function isAllowedUnderBlock(Block $block) : bool{
        $bk = $this->config->get('allow-block-under-id');
        $isBool = is_bool($bk);
        return $isBool && $bk ? true : ($isBool ? false : $block->getPos()->getWorld()->getBlock($block->getPos()->down())->getId() === $bk);
    }

    public function canUseWorld(World $level) : bool{
        $world = $this->config->get('apply-world');
        if(is_bool($world) && $world){
            return true;
        }else{
            foreach(explode(',', $world) as $w){
                if(strtolower($world->getName()) === strtolower(trim($w))) return true;
            }
        }
        return false;
    }

    public function isAllowedHighHeight(Player $player, Vector3 $pos) : bool{
        return $this->config->get('allow-seat-high-height') ? true : $player->getPosition()->getY() - $pos->getY() >= 0;
    }

    public function canSit(Player $player, Block $block) : bool{
        return $this->isStairBlock($block) &&
            $this->canUseWorld($player->getWorld()) &&
            $this->isAllowedHighHeight($player, $block->getPos()) &&
            $this->isAllowedUnderBlock($block);
    }

    public function isUsingSeat(Vector3 $pos) : ?Player{
        foreach($this->sit as $name => $data){
            if($pos->equals($data[1])){
                $player = Server::getInstance()->getPlayerExact($name);
                return $player;
            }
        }
        return null;
    }

    public function getSitData(Player $player, int $type = 2){
        return $this->sit[$player->getName()][$type];
    }

    public function setSitPlayerId(Player $player, Chair $block, int $id, Vector3 $pos) : void{
        $this->sit[$player->getName()] = [$block, $pos, $id];
    }

    public function isSitting(Player $player) : bool{
        return array_key_exists($player->getName(), $this->sit);
    }

    public function unsetSitting(Player $player){
        $id = $this->getSitData($player);
        $pk = new SetActorLinkPacket();
        $entLink = new EntityLink($id, $player->getId(), EntityLink::TYPE_REMOVE, true, true);
        $pk->link = $entLink;
        Server::getInstance()->broadcastPackets(Server::getInstance()->getOnlinePlayers(), [$pk]);
        $pk = RemoveEntityPacket::create($id);
        Server::getInstance()->broadcastPackets(Server::getInstance()->getOnlinePlayers(), [$pk]);
        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, false);
        unset($this->sit[$player->getName()]);
    }

    public function setSitting(Player $player, Vector3 $pos, Chair $block, int $id, ?Player $specific = null){
        $addEntity = new AddActorPacket();
        $addEntity->entityRuntimeId = $id;
        $addEntity->type = Zombie::getNetworkTypeId();
        $addEntity->position = $pos->add(0.5, $block->getChairHeight(), 0.5);
        $flags = (1 << EntityMetadataFlags::IMMOBILE | 1 << EntityMetadataFlags::SILENT | 1 << EntityMetadataFlags::INVISIBLE);
        $addEntity->metadata = [EntityMetadataProperties::FLAGS => new LongMetadataProperty($flags)];
        $setEntity = new SetActorLinkPacket();
        $entLink = new EntityLink($id, $player->getId(), EntityLink::TYPE_RIDER, true, true);
        $setEntity->link = $entLink;
        if($specific){
            $specific->getNetworkSession()->sendDataPacket($addEntity);
            $specific->getNetworkSession()->sendDataPacket($setEntity);
        }else{
            $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, true);
            $this->setSitPlayerId($player, $block, $id, $pos->floor());
            Server::getInstance()->broadcastPackets( Server::getInstance()->getOnlinePlayers(), [$addEntity]);
            Server::getInstance()->broadcastPackets( Server::getInstance()->getOnlinePlayers(), [$setEntity]);
        }
    }
}