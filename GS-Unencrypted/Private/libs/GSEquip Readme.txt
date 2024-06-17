GSEquip
-Auto equip gear (char, off hands and merc)
-Auto equip charms
-Auto use skill points
-Auto use stat points
-Easy to configure
-Can run 1 or 100 chars
-Works on all char types
-Can gear hammerdins, light sorcs, smiters, summon necros, etc all at the same time
-Easily add to any kol (even most edited ones)
-Auto sort inv
-Auto creates rune words based on config
-Auto cubes based on config
-Hires specafied merc

Hotkeys:
/ - Prints a list of all missing items in the D2Bot log panel.

How it works:
(1) Your drop gear
(2) Gear is picked based on your settings
(3) Gear is equipped to char, off hands, and merc
(4) Charms are picked based on your settings
(5) Buys Tomb
(6) Sorts inv

Config Help (GSEquipConfig.js)
	WaitMode
		Time - Waits a amount of time, checks items, prints missing in d2bot console and leaves game.
		Full - Waits for full equip.
	
	WaitTime
		Only used in Timed wait mode.
	
	UseRuneWords
		Enabled Kols internal rune word creation system (add rune words you want to make in char config).
	
	UseCube
		Enabled Kols internal cubing system same as above.
		
	IdentifyItems
		Identiy unid'd items.
	
	HireMercInNM
		If in Nightmare mode hire A2 merc with Holy Freeze.
	
	OnlyPickInNorm
		Only pickup items when in normal mode.
