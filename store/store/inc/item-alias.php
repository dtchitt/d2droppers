<?php

function IDToColor($id) {
	if($id == 3) return "Black";
	else if($id == 4) return "Light Blue";
	else if($id == 5) return "Dark Blue";
	else if($id == 6) return "Crystal Blue";
	else if($id == 7) return "Light Red";
	else if($id == 8) return "Dark Red";
	else if($id == 9) return "Crystal Red";
	else if($id == 11) return "Dark Green";
	else if($id == 12) return "Crystal Green";
	else if($id == 13) return "Light Yellow";
	else if($id == 14) return "Dark Yellow";
	else if($id == 15) return "Light Gold";
	else if($id == 16) return "Dark Gold";
	else if($id == 17) return "Light Purple";
	else if($id == 19) return "Orange";
	else if($id == 20) return "White";
}

function ColorToID($color) {
	if($color == "Black") return 3;
	else if($color == "Light Blue") return 4;
	else if($color == "Dark Blue") return 5;
	else if($color == "Crystal Blue") return 6;
	else if($color == "Light Red") return 7;
	else if($color == "Dark Red") return 8;
	else if($color == "Crystal Red") return 9;
	else if($color == "Dark Green") return 11;
	else if($color == "Crystal Green") return 12;
	else if($color == "Light Yellow") return 13;
	else if($color == "Dark Yellow") return 14;
	else if($color == "Light Gold") return 15;
	else if($color == "Dark Gold") return 16;
	else if($color == "Light Purple") return 17;
	else if($color == "Orange") return 19;
	else if($color == "White") return 20;
	else return false;
}

function AliasClassToName($id) {
	if($id == 0) return "Normal";
	elseif($id == 1) return "Exceptional";
	elseif($id == 2) return "Elite";
	else return "Unknown";
}

function AliasQualityToName($id) {
	if($id == 1) return "Low Quality";
	elseif($id == 2) return "Normal";
	elseif($id == 3) return "Superior";
	elseif($id == 4) return "Magic";
	elseif($id == 5) return "Set";
	elseif($id == 6) return "Rare";
	elseif($id == 7) return "Unique";
	elseif($id == 8) return "Crafted";
	else return "Unknown";
}

function AliasQualityToID($id) {
	if($id == "Low Quality") return 1;
	elseif($id == "Normal") return 2;
	elseif($id == "Superior") return 3;
	elseif($id == "Magic") return 4;
	elseif($id == "Set") return 5;
	elseif($id == "Rare") return 6;
	elseif($id == "Unique") return 7;
	elseif($id == "Crafted") return 8;
	else return "Unknown";
}

function AliasColorToName($id) {
	if($id == 3) return "Black";
	elseif($id == 20) return "White";
	elseif($id == 19) return "Orange";
	elseif($id == 13) return "Yellow";
	elseif($id == 7) return "Red";
	elseif($id == 15) return "Gold";
	elseif($id == 4) return "Blue";
	elseif($id == 17) return "Purple";
	elseif($id == 6) return "Crystal Blue";
	elseif($id == 9) return "Crystal Red";
	elseif($id == 12) return "Crystal Green";
	elseif($id == 14) return "Dark Yellow";
	elseif($id == 8) return "Dark Red";
	elseif($id == 16) return "Dark Gold";
	elseif($id == 11) return "Dark Green";
	elseif($id == 5) return "Dark Blue";
	else return "Plain";
}

function AliasFlagToName($flag) {
	if($flag == 0x10) return "Identified";
	else if($flag == 0x400000) return "Ethereal";
	else if($flag == 0x4000000) return "Runeword";
}

function TypIDToName($id) {
	if($id == 10) return "ring";
	else if($id == 82) return "small charm";
	else if($id == 83) return "large charm";
	else if($id == 84) return "grand charm";
	else if($id == 58) return "jewel";
	else if($id == 12) return "amulet";
}

function NameToTypeID($name) {
	$name = str_replace(" ", "", $name);
	if(strtolower($name) == "rune") return 74;
	else if(strtolower($name) == "shield") return 2;
	else if(strtolower($name) == "armor") return 3;
	else if(strtolower($name) == "ring") return 10;
	else if(strtolower($name) == "amulet") return 12;
	else if(strtolower($name) == "charm") return 13;
	else if(strtolower($name) == "boots") return 15;
	else if(strtolower($name) == "gloves") return 16;
	else if(strtolower($name) == "belt") return 19;
	else if(strtolower($name) == "gem") return 20;
	else if(strtolower($name) == "torch") return 21;
	else if(strtolower($name) == "scepter") return 24;
	else if(strtolower($name) == "wand") return 25;
	else if(strtolower($name) == "staff") return 26;
	else if(strtolower($name) == "bow") return 27;
	else if(strtolower($name) == "axe") return 28;
	else if(strtolower($name) == "club") return 29;
	else if(strtolower($name) == "sword") return 30;
	else if(strtolower($name) == "hammer") return 31;
	else if(strtolower($name) == "knife") return 32;
	else if(strtolower($name) == "spear") return 33;
	else if(strtolower($name) == "polearm") return 34;
	else if(strtolower($name) == "crossbow") return 35;
	else if(strtolower($name) == "mace") return 36;
	else if(strtolower($name) == "helm") return 37;
	else if(strtolower($name) == "quest") return 39;
	else if(strtolower($name) == "throwingknife") return 42;
	else if(strtolower($name) == "throwingaxe") return 43;
	else if(strtolower($name) == "javelin") return 44;
	else if(strtolower($name) == "weapon") return 45;
	else if(strtolower($name) == "meleeweapon") return 46;
	else if(strtolower($name) == "missileweapon") return 47;
	else if(strtolower($name) == "thrownweapon") return 48;
	else if(strtolower($name) == "comboweapon") return 49;
	else if(strtolower($name) == "anyarmor") return 50;
	else if(strtolower($name) == "anyshield") return 51;
	else if(strtolower($name) == "miscellaneous") return 52;
	else if(strtolower($name) == "socketfiller") return 53;
	else if(strtolower($name) == "secondhand") return 54;
	else if(strtolower($name) == "stavesandrods") return 55;
	else if(strtolower($name) == "missile") return 56;
	else if(strtolower($name) == "blunt") return 57;
	else if(strtolower($name) == "jewel") return 58;
	else if(strtolower($name) == "classspecific") return 59;
	else if(strtolower($name) == "amazonitem") return 60;
	else if(strtolower($name) == "barbarianitem") return 61;
	else if(strtolower($name) == "necromanceritem") return 62;
	else if(strtolower($name) == "paladinitem") return 63;
	else if(strtolower($name) == "sorceressitem") return 64;
	else if(strtolower($name) == "assassinitem") return 65;
	else if(strtolower($name) == "druiditem") return 66;
	else if(strtolower($name) == "handtohand") return 67;
	else if(strtolower($name) == "orb") return 68;
	else if(strtolower($name) == "voodooheads") return 69;
	else if(strtolower($name) == "auricshields") return 70;
	else if(strtolower($name) == "primalhelm") return 71;
	else if(strtolower($name) == "pelt") return 72;
	else if(strtolower($name) == "cloak") return 73;
	else if(strtolower($name) == "circlet") return 75;
	else if(strtolower($name) == "smallcharm") return 82;
	else if(strtolower($name) == "largecharm") return 83;
	else if(strtolower($name) == "grandcharm") return 84;
	else if(strtolower($name) == "amazonbow") return 85;
	else if(strtolower($name) == "amazonspear") return 86;
	else if(strtolower($name) == "amazonjavelin") return 87;
	else if(strtolower($name) == "assassinclaw") return 88;
	else if(strtolower($name) == "magicbowquiv") return 89;
	else if(strtolower($name) == "magicxbowquiv") return 90;
	else if(strtolower($name) == "chippedgem") return 91;
	else if(strtolower($name) == "flawedgem") return 92;
	else if(strtolower($name) == "standardgem") return 93;
	else if(strtolower($name) == "flawlessgem") return 94;
	else if(strtolower($name) == "perfectgem") return 95;
	else if(strtolower($name) == "amethyst") return 96;
	else if(strtolower($name) == "diamond") return 97;
	else if(strtolower($name) == "emerald") return 98;
	else if(strtolower($name) == "ruby") return 99;
	else if(strtolower($name) == "sapphire") return 100;
	else if(strtolower($name) == "topaz") return 101;
	else if(strtolower($name) == "skull") return 102;
	else return -1;
}