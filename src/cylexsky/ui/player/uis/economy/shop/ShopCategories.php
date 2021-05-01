<?php
declare(strict_types=1);

namespace cylexsky\ui\player\uis\economy\shop;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use cylexsky\misc\shop\objects\Category;
use cylexsky\misc\shop\ShopHandler;
use cylexsky\session\PlayerSession;
use cylexsky\ui\player\PlayerUIHandler;
use cylexsky\utils\Glyphs;
use pocketmine\player\Player;

class ShopCategories extends SimpleForm{

    private $session;
    private $cateogries = [];

    public function __construct(PlayerSession $session)
    {
        $this->session = $session;
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(Glyphs::C_RIGHT_ARROW . TextFormat::GOLD . "Shop" . Glyphs::C_LEFT_ARROW);
        $categories = ShopHandler::getCategories();
        $content = "===" . TextFormat::GRAY . "The shop allows your to\n";
        $content .= "===" . TextFormat::GRAY . "purchase many wonderful\n";
        $content .= "===" . TextFormat::GRAY . "things!";
        $this->setContent($content);
        foreach ($categories as $category){
            if ($category instanceof Category){
                $this->cateogries[] = $category;
                if ($category->getTexture() !== null){
                    $firstString = substr($category->getTexture(), 0, 4);
                    if ($firstString === "text"){
                        $this->addButton($category->getButtonName(), self::IMAGE_TYPE_PATH, $firstString);
                    }else{
                        $this->addButton($category->getButtonName(), self::IMAGE_TYPE_URL, $firstString);
                    }
                    continue;
                }
                $this->addButton($category->getButtonName());
            }
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data = null){
            if ($data === null){
                return;
            }
            PlayerUIHandler::sendShopCategoryItemList($this->session, $this->cateogries[$data]);
            return;
        };
    }


}