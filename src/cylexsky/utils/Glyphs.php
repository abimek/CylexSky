<?php
declare(strict_types=1);

namespace cylexsky\utils;

use core\main\text\TextFormat;

interface Glyphs{

    public const CYLEX = "cylex";

    public const SWORD_LEFT = "";
    public const SWORD_RIGHT = "";
    public const SKULL = "";

    public const BELLS = "";

    public const BOOK = "";

    public const OPAL = "";

    public const OPEN_BOOK = "";

    public const GOLD_COIN = "";

    //TODO CREATE GLYPHS
    public const GOLD_MEDAL = "";

    public const DISCORD = "";
    public const YOUTUBE = "";

    //bassically some mini starts in one thing(SPARKLES)
    public const SPARKLE = "";

    public const C_RIGHT_ARROW = "";
    public const C_LEFT_ARROW = "";

    public const BUBBLE_MESSAGE = "";
    public const CHEST = "";

    //arrow facing right direction
    public const RIGHT_ARROW = "";
    public const LEFT_ARROW = "";
    public const CROWN = "";
    public const BOX_EXCLAMATION = "";
    public const GREEN_BOX_EXCLAMATION = "";

    public const LINE = TextFormat::AQUA . "■"; //BLUE LINE FOR BOTTOM AND TOP OF MESSAGES

    //LEXY FAVE GLYPHS

    public const PRESTIGE_SHARDS = "s";

    public const LEXY_LINE_1 = "sss";
    public const LEXY_LINE_2 = "sss";
    public const LEXY_LINE_3 = "sss";

    public const JERRY_LINE_1 = "jjj";
    public const JERRY_LINE_2 = "jjj";
    public const JERRY_LINE_3 = "jjj";

    public const JERRY_32 = "jerry32";

    //SOME TYPE OF MEDAL >> https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.iconfinder.com%2Ficons%2F3028716%2Fgame_badge_level_star_icon&psig=AOvVaw2QmS_sIOqHYl7V9nRHMdZ3&ust=1616357180021000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCOCmyr7Kwu8CFQAAAAAdAAAAABAD
    public const LEVEL_ICON = "1";

    //https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.reddit.com%2Fr%2FPixelArt%2Fcomments%2Fbui7j5%2Fi_made_a_sunset_island_pic_with_a_16x16_canvas%2F&psig=AOvVaw1TT5K3x3eMPJoY7tnOfrX8&ust=1616357282716000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCNjviePKwu8CFQAAAAAdAAAAABAD
    public const ISLAND_ICON = "";

    public const CHECK_MARK = "c";
    public const X_MARK = "x";

    public const MINION = " minions";

    public const OWNER = "";
    public const DEV = "";
    public const ADMIN = "";
    public const MOD = "";
    public const HELPER = "";
    public const ROOKIE = "";
    public const ELITE = "";
    public const ARCHER = "";
    public const KNIGHT = "";
    public const MASTER = "";
    public const LORD = "";
    public const KING = "";

    //emojis
    //default
    public const SMILE_EMOJI = "\u{E18B}";
    public const LMAO_EMOJI = "\u{E177}";
    public const HEART_EYES_EMOJI = "\u{E17B}";
    public const WINK_EYE_EMOJI = "\u{E17E}";
    public const COOL_EMOJI = "\u{E181}";
    public const SAD_EMOJI = "\u{E182}";
    public const MAD_EMOJI = "\u{E189}";
    public const CLOWN_EMOJI = "\u{E199}";
    public const HEART_EMOJI = "\u{E19E}";

    //archer
    public const NERVOUS_EMOJI = "\u{E178}";
    public const SQUINT_EMOJI = "\u{E179}";

    //knight
    public const SHY_EMOJI = "\u{E18A}";
    public const SICK_EMOJI = "\u{E195}";

    //lord
    public const ASTONISHED_EMOJI = "\u{E18D}";
    public const SUSPICIOUS_EMOJI = "\u{E180}";

    //master
    public const GRIN_EMOJI = "\u{E190}";
    public const DEVIL_EMOJI = "\u{E198}";

    //elites
    public const SQUIRM_EMOJI = "\u{E183}";
    public const OOO_EMOJI = "\u{E193}";

    //king emojis
    public const KISS_EMOJI = "\u{E17C}";
    public const WINK_EMOJI = "\u{E185}";
    public const DEAD_EMOJI = "\u{E193}";

    public const BLUSH_SMILE_EMOJI = "\u{E176}";
    public const SMILE_DOWN_EMOJI = "\u{E17A}";
    public const TONGUE_EMOJI = "\u{E17D}";
    public const GOOGLIEYES_FACE_EMOJI = "\u{E17F}";
    public const HALF_CRY_EMOJI = "\u{E186}";
    public const CRY_EMOJI = "\u{E187}";
    public const MAD_FROWN_EMOJI = "\u{E188}";
    public const FREEZING_EMOJI = "\u{E18C}";
    public const SICK_SAD_EMOJI = "\u{E18E}";
    public const NEUTRAL_EMOJI = "\u{E18F}";
    public const GRIN_SMILE_EMOJI = "\u{E191}";
    public const SLEEPING_EMOJI = "\u{E192}";
    public const PUKE_EMOJI = "\u{E196}";
    public const MONEYFACE_EMOJI = "\u{E197}";


}