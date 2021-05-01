<?php
declare(strict_types=1);

namespace cylexsky\session\modules;

use cylexsky\utils\Glyphs;

class EmojiModule extends BaseModule{

    public const MAX_EMOJIS = 5;

    public const EMOJIS = [
        ":smile:" => Glyphs::SMILE_EMOJI,
        ":blush_smile:" => Glyphs::BLUSH_SMILE_EMOJI,
        ":lmao:" => Glyphs::LMAO_EMOJI,
        ":nervous:" => Glyphs::NERVOUS_EMOJI,
        ":squint:" => Glyphs::SQUINT_EMOJI,
        ":smile_down:" => Glyphs::SMILE_DOWN_EMOJI,
        ":heart_eyes:" => Glyphs::HEART_EYES_EMOJI,
        ":kiss:" => Glyphs::KISS_EMOJI,
        ":tongue:" => Glyphs::TONGUE_EMOJI,
        ":wink_eye:" => Glyphs::WINK_EYE_EMOJI,
        ":googli_face:" => Glyphs::GOOGLIEYES_FACE_EMOJI,
        ":suspicious:" => Glyphs::SUSPICIOUS_EMOJI,
        ":cool:" => Glyphs::COOL_EMOJI,
        ":sad:" => Glyphs::SAD_EMOJI,
        ":squirm:" => Glyphs::SQUIRM_EMOJI,
        ":wink:" => Glyphs::WINK_EMOJI,
        ":half_cry:" => Glyphs::HALF_CRY_EMOJI,
        ":cry:" => Glyphs::CRY_EMOJI,
        ":mad_frown:" => Glyphs::MAD_FROWN_EMOJI,
        ":mad:" => Glyphs::MAD_EMOJI,
        ":shy:" => Glyphs::SHY_EMOJI,
        ":freezing:" => Glyphs::FREEZING_EMOJI,
        ":astonished:" => Glyphs::ASTONISHED_EMOJI,
        ":sick_sad:" => Glyphs::SICK_SAD_EMOJI,
        ":neutral" => Glyphs::NEUTRAL_EMOJI,
        ":grin:" => Glyphs::GRIN_EMOJI,
        ":grin_smile:" => Glyphs::GRIN_SMILE_EMOJI,
        ":sleeping:" => Glyphs::SLEEPING_EMOJI,
        ":ooo:" => Glyphs::OOO_EMOJI,
        ":dead:" => Glyphs::DEAD_EMOJI,
        ":sick:" => Glyphs::SICK_EMOJI,
        ":puke:" => Glyphs::PUKE_EMOJI,
        ":money_face:" => Glyphs::MONEYFACE_EMOJI,
        ":devil:" => Glyphs::DEVIL_EMOJI,
        ":clown:" => Glyphs::CLOWN_EMOJI,
        ":heart:" => Glyphs::HEART_EMOJI
    ];

    public const DEFAULT_EMOJIS = [
        ":smile:",
        ":lmao:",
        ":heart_eyes:",
        ":wink_eye:",
        ":cool:",
        ":sad:",
        ":mad:",
        ":clown:",
        ":heart:"
    ];

    private $emojis = [];

    public function init(array $data)
    {
        $this->emojis = $data;
    }

    public function replaceEmojis(string &$message){
        $count = 0;
        $emojis = $this->emojis;
        if ($this->getSession()->isStaff()){
            $emojis = array_keys(self::EMOJIS);
        }
        foreach ($emojis as $key){
            if ($count >= self::MAX_EMOJIS){
                break;
            }
            $emoji = self::EMOJIS[$key];
            while(strpos($message, $key) !== false){
                if ($count >= self::MAX_EMOJIS){
                    break;
                }
                $count++;
                $message = self::replace_first_str($key, $emoji, $message);
            }
        }
    }

    public function getEmojis(){
        return $this->emojis;
    }

    public function getEmojisMapped(){
        $maps = [];
        foreach ($this->emojis as $key){
            $maps[$key] = self::EMOJIS[$key];
        }
        return $maps;
    }

    public function getEmojiCharacters(){
        return array_map(function ($emoji){
            return self::EMOJIS[$emoji];
        }, $this->emojis);
    }

    public function getNotUnlockedEmojiCharacters(){
        return array_filter(array_map(function ($key){
            if (!isset($this->emojis[$key])){
                return self::EMOJIS[$key];
            }
        }, array_keys(self::EMOJIS)));
    }

    public function getNotUnlockedEmojisMapped(){
        $mapped = [];

        $array =  array_filter(array_map(function ($key){
            if (!in_array($key, $this->emojis)){
                return $key;
            }
        }, array_keys(self::EMOJIS)));
        foreach ($array as $key){
            $mapped[$key] = self::EMOJIS[$key];
        }
        return $mapped;
    }

    public function addEmojis(array $emojis){
        $this->getSession()->setHasBeenChanged();
        $this->emojis = array_merge($emojis, $this->emojis);
    }

    public static function replace_first_str($search_str, $replacement_str, $src_str){
        return (false !== ($pos = strpos($src_str, $search_str))) ? substr_replace($src_str, $replacement_str, $pos, strlen($search_str)) : $src_str;
    }

    public static function getBaseData(): array
    {
        return self::DEFAULT_EMOJIS;
    }

    public static function getArcherEmojis(): array {
        return [
            ":nervous:",
            ":squint:"
        ];
    }

    public static function getKnightEmojis(): array {
        return array_merge(self::getArcherEmojis(), [
            ":shy:",
            ":sick:"
        ]);
    }

    public static function getLordEmojis(): array {
        return array_merge(self::getKnightEmojis(), [
            ":astonished:",
            ":suspicious:"
        ]);
    }

    public static function getMasterEmojis(): array {
        return array_merge(self::getLordEmojis(), [
            ":grin:",
            ":devil:"
        ]);
    }

    public static function getEliteEmojis(): array {
        return array_merge(self::getMasterEmojis(), [
            ":squirm:",
            ":ooo:"
        ]);
    }

    public static function getKingEmojis():array {
        return array_merge(self::getEliteEmojis(), [
            ":kiss:",
            ":wink:",
            ":dead:"
        ]);
    }

    public function save()
    {
        return $this->encodeJson($this->emojis);
    }
}