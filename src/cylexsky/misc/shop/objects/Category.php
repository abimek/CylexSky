<?php
declare(strict_types=1);

namespace cylexsky\misc\shop\objects;

use pocketmine\utils\Config;

class Category{

    private $items = [];

    private $name;
    private $texture;
    private $buttonName;

    private $config;

    public function __construct(string $name, ?string $texture, string $buttonName, Config $config)
    {
        $this->name = $name;
        $this->texture = $texture;
        $this->buttonName = $buttonName;
        $this->config = $config;
        $this->load();
    }

    public function load()
    {
        foreach ($this->config->getAll() as $id => $data){
            $this->items[$id] = new ShopItem($data);
        }
    }

    public function getName(): string {return $this->name;}
    public function getTexture(): ?string{return $this->texture;}
    public function getButtonName(): string {return $this->buttonName;}

    public function getShopItems(): array {
        return $this->items;
    }

    public function save(){
        foreach ($this->items as $id => $shopItem){
            $this->config->setNested($id, $shopItem->save());
        }
        $this->config->save();
    }
}