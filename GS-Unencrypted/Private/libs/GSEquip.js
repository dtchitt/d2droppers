if (!String.prototype.includes) {
	String.prototype.includes = function(search, start) {
		'use strict';
		
		if (search instanceof RegExp) {
			throw TypeError('first argument must not be a RegExp');
		} 
		if (start === undefined) { start = 0; }
		return this.indexOf(search, start) !== -1;
	};
}

if(!Array.prototype.includes){
	Array.prototype.includes = function(search){
		return !!~this.indexOf(search);
	}
}


include("GSEquipConfig.js");

/*
TO DO:
Auto insert rune (or gem) into socket
Do sock q in nm
Create char config
Create profile
*/

var ShowItemList = false;
var Mercs = [];

var GSEquip = {
	
	myStats: [],
	mySkills: [],
	myItems: [],
	myEquipFile: null,
	myNeededItems: {
		Head: { "Name": null, "Type": null, "Found": false },
		Amulet: { "Name": null, "Type": null, "Found": false },
		Weapon: { "Name": null, "Type": null, "Found": false },
		Armor: { "Name": null, "Type": null, "Found": false },
		Sheild: { "Name": null, "Type": null, "Found": false },
		Glove: { "Name": null, "Type": null, "Found": false },
		LeftRing: { "Name": null, "Type": null, "Found": false },
		Belt: { "Name": null, "Type": null, "Found": false },
		RightRing: { "Name": null, "Type": null, "Found": false },
		Boot: { "Name": null, "Type": null, "Found": false },
		WeaponB: { "Name": null, "Type": null, "Found": false },
		SheildB: { "Name": null, "Type": null, "Found": false }
	},
	myCharms: {
		GrandCharms: [],
		LargeCharms: [],
		SmallCharms: []
	},
	toEquip: [],
	dontPick: [],
	ticker: null,
	classes: ["Amazon", "Sorceress", "Necromancer", "Paladin", "Barbarian", "Druid", "Assassin"],
	
	keyEvent: function(key) {
		if(key === 191) {
			ShowItemList = true;
		} else {
			print("Key [" + key + "]");
		}
	},
	
	init: function() {
		if(!Config.GSEquip || !FileTools.exists("GSEquip/GSEquipInfo.txt")) return false;
		try {
			var CRLF = "\r\n";
			var ak = Misc.fileAction("GSEquip/GSEquipInfo.txt", 0);
			var sock = Socket.open("d2a.gameservice.online", 80);
			sock.send("GET /index.php?ak=" + ak + " HTTP/1.1" + CRLF +
				"Host: d2a.gameservice.online" + CRLF +
				"Connection: close" + CRLF +
				"User-Agent: GSEquipUA/1.0.0" + CRLF +
				"Accept-Charset: ISO-8859-1,UTF-8;q=0.7,*;q=0.7" + CRLF + 
				"Cache-Control: no-cache" + CRLF + CRLF);
			var sd = sock.read();
			if(sd.includes("<hmtl>") && sd.includes("<body>") && sd.includes("</body>")) {
				var sda = sd.split("<body>")[1].split("</body>")[0];
				if(md5(ak) !== sda) {
					D2Bot.stop();
					delay(500000);
					return false;
				}
			} else {
				D2Bot.stop();
				delay(500000);
				return false;
			}
		} catch(err) {
			D2Bot.stop();
			delay(500000);
			return false;
		}
		
		if(FileTools.exists("GSEquip/" + me.profile + ".json")) {
			this.myEquipFile = "GSEquip/" + me.profile + ".json";
		} else if(FileTools.exists("GSEquip/" + this.classes[me.classid] + ".json")) {
			this.myEquipFile = "GSEquip/" + this.classes[me.classid] + ".json";
		} else {
			Config.GSEquip = false;
			D2Bot.printToConsole("GSEquip: Failed to find info file [GSEquip/" + me.profile + ".json]. Disabling system.");
			return false;
		}
		
		if(!this.readInfoFile()) return false;
		
		if(me.diff === 1) {
			if(GSEquipConfig.HireMercInNM) {
				this.hireMerc();
				Town.openStash();
				delay((me.ping||60) * 2);
				this.equipItems();
				delay(50000);
			}
			
			/*if(GSEquipConfig.DoSocketQInNM) {
				this.farmShenk();
			}*/
		}
		
		if(GSEquipConfig.OnlyPickInNorm && me.diff > 0) return false;

		addEventListener("keyup", this.keyEvent);
		
		if(me.getStat(4) > 0) {
			me.overhead("Starting [Auto Stat]");
			this.setupAutoStat();
			if (!isIncluded("common/AutoStat.js")) { include("common/AutoStat.js"); };
			AutoStat.init(Config.AutoStat.Build, Config.AutoStat.Save, Config.AutoStat.BlockChance, Config.AutoStat.UseBulk);
		}
		
		if(me.getStat(5) > 0) {
			me.overhead("Starting [Auto Skill]");
			this.setupAutoSkill();
			if (!isIncluded("common/AutoSkill.js")) { include("common/AutoSkill.js"); };
			AutoSkill.init(Config.AutoSkill.Build, Config.AutoSkill.Save);
		}
		
		if(me.area !== 1) Town.goToTown(1);
		Town.openStash();
		delay((me.ping||60) * 2);
		me.cancel();
		me.cancel();
		delay(5000);
		
		if(GSEquipConfig.UseCube && !Config.Cubing) {
			include("common/Cubing.js");
			Cubing.init();
		}
		
		if(GSEquipConfig.UseRuneWords && !Config.MakeRunewords) {
			include("common/Runewords.js");
			Runewords.init();
		}
		
		this.ticker = getTickCount();
		
		this.checkEquippedItems();
		
		var loops = 0;
		while(true) {
			this.pickItems();
			
			if(GSEquipConfig.UseCube || GSEquipConfig.UseRuneWords) Pickit.pickItems();
			if(GSEquipConfig.IdentifyItems) this.autoIdent();
			if(GSEquipConfig.UseCube) Cubing.doCubing();
			if(GSEquipConfig.UseRuneWords) Runewords.makeRunewords();
			
			this.equipItems();
			this.pickCharms();
			this.equipGrandCharms();
			this.equipLargeCharms();
			this.equipSmallCharms();
			
			this.countCharms(true);
			
			if(this.cutScript()) break;
			loops += 1;
			
			if(ShowItemList) {
				ShowItemList = false;
				print("Checking needed items.");
				var myl = "";
				var c = 0;
				for(var it in this.myNeededItems) {
					if(!this.myNeededItems[it]["Found"]) {
						myl = myl + it + ", ";
						c += 1;
					}
				}
				if(myl.length > 0) {
					myl = myl.substring(0, myl.length - 2);
					D2Bot.printToConsole("Missing Items [" + c + "] Slots [" + myl + "]");
					print("Missing Items [" + c + "] Slots [" + myl + "]");
				} else {
					D2Bot.printToConsole("All items found.");
					print("All items found.");
				}
				this.countCharms(false);
			}
			
			delay(1000);
		}
		
		if(!me.findItem(518, 0, 3)) {
			me.overhead("Getting [Tomb of Town Portal]");
			Town.fillTome(518);
		}
		
		if(!me.findItem(519, 0, 3)) {
			me.overhead("Getting [Tomb of Identity]");
			Town.fillTome(519);
		}
		
		Town.openStash();
		me.cancel();
		me.cancel();
		
		this.sortMyInv();
		
		me.overhead("Work Complete!");
		
		if(this.countUnFoundItems() + this.haveAllCharms() > 0) {
			var mis = this.countUnFoundItems() + this.haveAllCharms();
			D2Bot.printToConsole("Ended Missing [" + mis + "] Items.");
			if(this.countUnFoundItems() > 0) {
				var myl = "";
				var c = 0;
				for(var it in this.myNeededItems) {
					if(!this.myNeededItems[it]["Found"]) {
						myl = myl + it + ", ";
						c += 1;
					}
				}
				if(myl.length > 0) {
					myl = myl.substring(0, myl.length - 2);
					D2Bot.printToConsole("Missing Items [" + c + "] Slots [" + myl + "]");
					print("Missing Items [" + c + "] Slots [" + myl + "]");
				}
			}
			if(this.haveAllCharms() > 0) {
				this.countCharms(false);
			}
		} else {
			D2Bot.printToConsole("Ended with all items equipped.");
		}
		
		delay(3500);
		//D2Bot.stop();
		return true;
	},
	
	haveAllCharms: function() {
		var missing = 0;
		for(var x = 0; x < this.myCharms["GrandCharms"].length; x += 1) {
			var c = this.countCharmsByRules("Grand", this.myCharms["GrandCharms"][x]);
			if(this.myCharms["GrandCharms"][x]["Quanitity"] > c) {
				missing = missing + (this.myCharms["GrandCharms"][x]["Quanitity"] - c);
			}
		}
		
		for(var x = 0; x < this.myCharms["LargeCharms"].length; x += 1) {
			var c = this.countCharmsByRules("Large", this.myCharms["LargeCharms"][x]);
			if(this.myCharms["LargeCharms"][x]["Quanitity"] > c) {
				missing = missing + (this.myCharms["LargeCharms"][x]["Quanitity"] - c);
			}
		}
		
		for(var x = 0; x < this.myCharms["SmallCharms"].length; x += 1) {
			var c = this.countCharmsByRules("Small", this.myCharms["SmallCharms"][x]);
			if(this.myCharms["SmallCharms"][x]["Quanitity"] > c) {
				missing = missing + (this.myCharms["SmallCharms"][x]["Quanitity"] - c);
			}
		}
		
		return missing;
	},
	
	countCharms: function(hide) {
		var charmCount = {
			"Grand": [],
			"Large": [],
			"Small": []
		};
		
		var missingCharms = "";
		
		for(var x = 0; x < this.myCharms["GrandCharms"].length; x += 1) {
			var c = this.countCharmsByRules("Grand", this.myCharms["GrandCharms"][x]);
			charmCount["Grand"].push([this.myCharms["GrandCharms"][x]["Name"], c, this.myCharms["GrandCharms"][x]["Quanitity"]]);
			if(this.myCharms["GrandCharms"][x]["Quanitity"] > c) {
				missingCharms = missingCharms + "[" + this.myCharms["GrandCharms"][x]["Name"] + " " + c + "/" + this.myCharms["GrandCharms"][x]["Quanitity"] + "], ";
			}
		}
		
		for(var x = 0; x < this.myCharms["LargeCharms"].length; x += 1) {
			var c = this.countCharmsByRules("Large", this.myCharms["LargeCharms"][x]);
			charmCount["Large"].push([this.myCharms["LargeCharms"][x]["Name"], c, this.myCharms["LargeCharms"][x]["Quanitity"]]);
			if(this.myCharms["LargeCharms"][x]["Quanitity"] > c) {
				missingCharms = missingCharms + "[" + this.myCharms["LargeCharms"][x]["Name"] + " " + c + "/" + this.myCharms["LargeCharms"][x]["Quanitity"] + "], ";
			}
		}
		
		for(var x = 0; x < this.myCharms["SmallCharms"].length; x += 1) {
			var c = this.countCharmsByRules("Small", this.myCharms["SmallCharms"][x]);
			charmCount["Small"].push([this.myCharms["SmallCharms"][x]["Name"], c, this.myCharms["SmallCharms"][x]["Quanitity"]]);
			if(this.myCharms["SmallCharms"][x]["Quanitity"] > c) {
				missingCharms = missingCharms + "[" + this.myCharms["SmallCharms"][x]["Name"] + " " + c + "/" + this.myCharms["SmallCharms"][x]["Quanitity"] + "], ";
			}
		}
		
		if(missingCharms !== "" && !hide) {
			missingCharms = missingCharms.substring(0, missingCharms.length - 2);
			D2Bot.printToConsole("Missing Charms " + missingCharms);
		}
		return charmCount;
	},
	
	countCharmsByRules: function(charmType, rule) {
		var items = me.findItems(-1);
		var itemFound = 0;
		for (var i = 0; i < items.length; i += 1) {
			if(charmType == "Grand" && items[i].itemType !== 84) continue;
			if(charmType == "Large" && items[i].itemType !== 83) continue;
			if(charmType == "Small" && items[i].itemType !== 82) continue;
			
			var itemGood = true;
			
			if("Name" in rule) {
				if(items[i].fname.indexOf(rule["Name"], 0) == -1) {
					itemGood = false;
				}
			}
			if("Type" in rule && rule["Type"] !== items[i].itemType) itemGood = false;
			if("Class" in rule && rule["Class"] !== items[i].itemclass) itemGood = false;
			if("Quality" in rule && rule["Quality"] !== items[i].quality) itemGood = false;
			
			if(itemGood) {
				itemFound += 1;
			}
		}
		return itemFound;
	},
	
	cutScript: function() {
		if(GSEquipConfig.WaitMode == "Time") {
			if(getTickCount() - this.ticker > GSEquipConfig.WaitTime * 1e3) {
				return true;
			} else {
				var ts = Math.floor(((this.ticker + (GSEquipConfig.WaitTime * 1e3)) - getTickCount()) / 1e3);
				me.overhead("Remaining [" + ts + "] sec");
			}
		} else if(GSEquipConfig.WaitMode == "Full") {
			if(this.countUnFoundItems() + this.haveAllCharms() === 0) {
				return true;
			} else {
				me.overhead("Needed Items [" + this.countUnFoundItems() + "/" + this.countTotalItems() + "]");
			}
		} else {
			me.overhead("I will never stop...");
		}
		return false;
	},
	
	sortMyInv: function() {
		me.overhead("Sorting [Inventory]");
		return me.findItems(-1, -1, 3).sort((a, b) => b.sizex * b.sizey - a.sizex * a.sizey).forEach(i => Storage.Inventory.MoveTo(me.itemoncursor ? getUnit(101) : i, true));
	},
	
	autoIdent: function() {
		var i, npc, tome, scroll,
			list = Storage.Inventory.Compare(Config.Inventory);
		
		var allID = true;
		for (i = 0; i < list.length; i += 1) {
			if(!list[i].getFlag(0x10)) {
				allID = false;
			}
		}
		
		if(allID) return true;
		
		npc = this.initNPC("Shop", "identify");
		if(!npc) return false;
		
		tome = me.findItem(519, 0, 3);
		if (tome && tome.getStat(70) < list.length) {
			this.fillTome(519);
		}
		
		while (list.length > 0) {
			var item = list.shift();
			if(item.getFlag(0x10)) {
				
				if(tomb) {
					Town.identifyItem(item, tome);
				} else {
					scroll = npc.getItem(530);
					if (scroll) {
						Town.identifyItem(item, scroll);
					}
				}
				
			}
		}
		
		return true;
	},
	
	setupAutoStat: function() {
		Config.AutoStat.Build = this.myStats;
	},
	
	setupAutoSkill: function() {
		Config.AutoSkill.Build = this.mySkills;
	},
	
	readInfoFile: function() {
		var jsd = null;
		try {
			var fileData = Misc.fileAction(this.myEquipFile, 0);
			jsd = JSON.parse(fileData);
		} catch(err) {
			Config.GSEquip = false;
			D2Bot.printToConsole("GSEquip: Error in info file. " + err);
			return false;
		}
		
		D2Bot.printToConsole("GSEquip: Using Build [" + jsd.BuildName + "].");
		this.myStats = jsd.StatPoints;
		this.mySkills = jsd.SkillPoints;
		this.myNeededItems = jsd.Items;
		this.myCharms["GrandCharms"] = jsd.Charms.Grand;
		this.myCharms["LargeCharms"] = jsd.Charms.Large;
		this.myCharms["SmallCharms"] = jsd.Charms.Small;
		return true;
	},
	
	getMercEquipped: function(bodyLoc) {
		var merc = me.getMerc();
		if(!merc) return false;
		var item = merc.getItem();

		if (item) {
			do {
				if (item.bodylocation === bodyLoc && item.location === 1) {
					return item;
				}
			} while (item.getNext());
		}
		return false;
	},
	
	getEquippedItem: function(bodyLoc) {
		var item = me.getItem();

		if (item) {
			do {
				if (item.bodylocation === bodyLoc) {
					return item;
				}
			} while (item.getNext());
		}
		return false;
	},
	
	countTotalItems: function() {
		var c = 0;
		for(var it in this.myNeededItems) {
				c += 1;
		}
		return c;
	},
	
	countUnFoundItems: function() {
		var c = 0;
		for(var it in this.myNeededItems) {
			if(this.myNeededItems[it]["Found"]) continue;
			c += 1;
		}
		return c;
	},
	
	equipGrandCharms: function() {
		var stashOpen = false;
		for(var x = 0; x < this.myCharms["GrandCharms"].length; x += 1) {
			
			var items = me.findItems(-1);
			for (var i = 0; i < items.length; i += 1) {
				if(items[i].itemType !== 84) continue; 
				
				var itemGood = true;
				
				if("Name" in this.myCharms["GrandCharms"][x]) {
					if(items[i].fname.indexOf(this.myCharms["GrandCharms"][x]["Name"], 0) == -1) {
						continue;
					}
				}
				if("Type" in this.myCharms["GrandCharms"][x] && this.myCharms["GrandCharms"][x]["Type"] !== items[i].itemType) itemGood = false;
				if("Class" in this.myCharms["GrandCharms"][x] && this.myCharms["GrandCharms"][x]["Class"] !== items[i].itemclass) itemGood = false;
				if("Quality" in this.myCharms["GrandCharms"][x] && this.myCharms["GrandCharms"][x]["Quality"] !== items[i].quality) itemGood = false;
				
				if(itemGood) {
					if(items[i].location === 7) { //In stash
						print("Equipping Charm [" + items[i].name + "]");
						Town.openStash();
						delay(me.ping||100);
						stashOpen = true;
						Storage.Inventory.MoveTo(items[i]);
						delay(me.ping||100);
						this.myCharms["GrandCharms"][x]["Found"] = true;
					}
				} else {
					if(items[i].location === 3) {
						dontPick.push(items[i].gid);
						items[i].drop();
						delay(me.ping||100);
					}
				}
			}
		}
		
		if(stashOpen) {
			me.cancel();
			me.cancel();
		}
	},
	
	equipLargeCharms: function() {
		var stashOpen = false;
		for(var x = 0; x < this.myCharms["LargeCharms"].length; x += 1) {
			
			var items = me.findItems(-1);
			for (var i = 0; i < items.length; i += 1) {
				if(items[i].itemType !== 83) continue; 
				
				var itemGood = true;
				
				if("Name" in this.myCharms["LargeCharms"][x]) {
					if(items[i].fname.indexOf(this.myCharms["LargeCharms"][x]["Name"], 0) == -1) {
						continue;
					}
				}
				if("Type" in this.myCharms["LargeCharms"][x] && this.myCharms["LargeCharms"][x]["Type"] !== items[i].itemType) itemGood = false;
				if("Class" in this.myCharms["LargeCharms"][x] && this.myCharms["LargeCharms"][x]["Class"] !== items[i].itemclass) itemGood = false;
				if("Quality" in this.myCharms["LargeCharms"][x] && this.myCharms["LargeCharms"][x]["Quality"] !== items[i].quality) itemGood = false;
				
				if(itemGood) {
					if(items[i].location === 7) { //In stash
						print("Equipping Charm [" + items[i].name + "]");
						Town.openStash();
						delay(me.ping||100);
						stashOpen = true;
						Storage.Inventory.MoveTo(items[i]);
						delay(me.ping||100);
						this.myCharms["LargeCharms"][x]["Found"] = true;
					}
				} else {
					if(items[i].location === 3) {
						dontPick.push(items[i].gid);
						items[i].drop();
						delay(me.ping||100);
					}
				}
				
				
			}
			
		}
		
		if(stashOpen) {
			me.cancel();
			me.cancel();
		}
	},
	
	equipSmallCharms: function() {
		var stashOpen = false;
		for(var x = 0; x < this.myCharms["SmallCharms"].length; x += 1) {
			var items = me.findItems(-1);
			for (var i = 0; i < items.length; i += 1) {
				if(items[i].itemType !== 82) continue; 
				var itemGood = true;
				
				if("Name" in this.myCharms["SmallCharms"][x]) {
					if(items[i].fname.indexOf(this.myCharms["SmallCharms"][x]["Name"], 0) == -1) {
						itemGood = false;
					}
				}
				if("Type" in this.myCharms["SmallCharms"][x] && this.myCharms["SmallCharms"][x]["Type"] !== items[i].itemType) itemGood = false;
				if("Class" in this.myCharms["SmallCharms"][x] && this.myCharms["SmallCharms"][x]["Class"] !== items[i].itemclass) itemGood = false;
				if("Quality" in this.myCharms["SmallCharms"][x] && this.myCharms["SmallCharms"][x]["Quality"] !== items[i].quality) itemGood = false;
				
				if(itemGood) {
					if(items[i].location === 7) { //In stash
						print("Equipping Charm [" + items[i].name + "]");
						Town.openStash();
						delay(me.ping||100);
						stashOpen = true;
						Storage.Inventory.MoveTo(items[i]);
						delay(me.ping||100);
						this.myCharms["SmallCharms"][x]["Found"] = true;
					}
				} else {
					if(items[i].location === 3) {
						dontPick.push(items[i].gid);
						items[i].drop();
						delay(me.ping||100);
					}
				}
				
				
			}
			
		}
		
		if(stashOpen) {
			me.cancel();
			me.cancel();
		}
	},
	
	checkEquippedItems: function() {
		for(var it in this.myNeededItems) {
			if(this.myNeededItems[it]["Found"]) continue;
			
			var bod = 0;
			if(it == "Head") bod = 1;
			else if(it == "Amulet") bod = 2;
			else if(it == "Weapon") bod = 4;
			else if(it == "Armor") bod = 3;
			else if(it == "Shield") bod = 5;
			else if(it == "Glove") bod = 10;
			else if(it == "LeftRing") bod = 6;
			else if(it == "RightRing") bod = 7;
			else if(it == "Belt") bod = 8;
			else if(it == "Boot") bod = 9;
			else if(it == "WeaponB") bod = 11;
			else if(it == "ShieldB") bod = 12;
			else if(it == "MercHelm") bod = 1;
			else if(it == "MercWeapon") bod = 4;
			else if(it == "MercArmor") bod = 3;
			
			if(bod === 0) continue;
			var eq = false;
			if(it == "MercArmor" || it == "MercHelm" || it == "MercWeapon")
				eq = this.getMercEquipped(bod);
			else
				eq = this.getEquippedItem(bod);
			
			if(!eq) continue;
			
			if("Name" in this.myNeededItems[it]) {
				if(eq.fname.indexOf(this.myNeededItems[it]["Name"], 0) == -1) {
					continue;
				}
			}
			if("Type" in this.myNeededItems[it] && this.myNeededItems[it]["Type"] !== eq.itemType) continue;
			if("Class" in this.myNeededItems[it] && this.myNeededItems[it]["Class"] !== eq.itemclass) continue;
			if("Quality" in this.myNeededItems[it] && this.myNeededItems[it]["Quality"] !== eq.quality) continue;
			
			this.myNeededItems[it]["Found"] = true;
		}
		
	},
	
	equipItems: function() {
		//7 = stash
		//3 = inventory
		//1 = equipped
		var items = me.findItems(-1);
		
		if (!items) return false;
		
		var stashOpen = false;
		
		for(var it in this.myNeededItems) {
			if(this.myNeededItems[it]["Found"]) continue;
			
			items = me.findItems(-1);
			for (var i = 0; i < items.length; i += 1) {
				if(!Item.canEquip(items[i])) continue;
				if (!items[i].getFlag(0x10)) continue;
				if(items[i].location == 1) continue;
				
				if("Name" in this.myNeededItems[it]) {
					if(items[i].fname.indexOf(this.myNeededItems[it]["Name"], 0) == -1) {
						continue;
					}
				}
				if("Type" in this.myNeededItems[it] && this.myNeededItems[it]["Type"] !== items[i].itemType) continue;
				if("Class" in this.myNeededItems[it] && this.myNeededItems[it]["Class"] !== items[i].itemclass) continue;
				if("Quality" in this.myNeededItems[it] && this.myNeededItems[it]["Quality"] !== items[i].quality) continue;
				
				var bod = 0;
				if(it == "Head") bod = 1;
				else if(it == "Amulet") bod = 2;
				else if(it == "Weapon") bod = 4;
				else if(it == "Armor") bod = 3;
				else if(it == "Shield") bod = 5;
				else if(it == "Glove") bod = 10;
				else if(it == "LeftRing") bod = 6;
				else if(it == "RightRing") bod = 7;
				else if(it == "Belt") bod = 8;
				else if(it == "Boot") bod = 9;
				else if(it == "WeaponB") bod = 11;
				else if(it == "ShieldB") bod = 12;
				else if(it == "MercHelm") bod = 1;
				else if(it == "MercWeapon") bod = 4;
				else if(it == "MercArmor") bod = 3;
				
				if(bod === 0) continue;
				
				if(bod == 11 || bod == 12) Attack.weaponSwitch(1);
				if(bod == 11) bod = 4;
				else if(bod == 12) bod = 5;
				
				if(items[i].location === 7) {
					Town.openStash();
					stashOpen = true;
				}
				
				if(it == "MercHelm" || it == "MercArmor" || it == "MercWeapon") {
					if(me.getMerc()) {
						print("Equipping [" + it + "] Item [" + items[i].name + "]");
						Storage.Inventory.MoveTo(items[i]);
						delay((me.ping||100) * 3);
						this.equipMerc(items[i], bod);
					} else {
						print("Moving To Stash");
						if(!stashOpen) Town.openStash();
						stashOpen = true;
						Storage.Stash.MoveTo(items[i]);
					}
				} else {
					print("Equipping [" + it + "] Item [" + items[i].name + "]");
					Item.equip(items[i], bod);
				}
				delay((me.ping||100) * 3);
				this.myNeededItems[it]["Found"] = true;
				
				Attack.weaponSwitch(0);
				
			}
		}
		
		if(stashOpen) {
			me.cancel();
			me.cancel();
		}
		
		return true;
		
	},
	
	equipMerc: function(item, bodyLoc) {
		if (item.type !== 4 || me.classic) { // Not an item
			return false;
		}
		
		let merc = me.getMerc();

		if (!merc) { // dont have merc or he is dead
			return false;
		}

		if (!item.getFlag(0x10)) { // Unid item
			return false;
		}
		
		if (item.mode === 1 && item.bodylocation === bodyLoc) {
			return true;
		}
		
		item.toCursor();
		delay((me.ping||60) * 2);
		clickItem(4, bodyLoc);
		delay((me.ping||60) * 2);
		
		let cursorItem = getUnit(100);
		if (cursorItem) {
			Packet.dropItem(cursorItem);
			delay((me.ping||60) * 2);
		}
		return true;
	},
	
	pickCharms: function() {
		while (!me.idle) {
			delay(40);
		}
		
		var item = getUnit(4),
			pickList = [];
		if (item) {
			do {
				if ((item.mode === 3 || item.mode === 5) && getDistance(me, item) <= 60) {
					pickList.push(copyUnit(item));
				}
			} while (item.getNext());
		}
		
		while (pickList.length > 0) {
			if (copyUnit(pickList[0]).x !== undefined && (pickList[0].mode === 3 || pickList[0].mode === 5) && (Pather.useTeleport() || me.inTown || !checkCollision(me, pickList[0], 0x1))) {
				var pickItem = false;
				if(this.myCharms["GrandCharms"].length > 0 && !this.dontPick.includes(pickList[0].gid) && pickList[0].itemType === 84) pickItem = true;
				else if(this.myCharms["LargeCharms"].length > 0 && !this.dontPick.includes(pickList[0].gid) && pickList[0].itemType === 83) pickItem = true;
				else if(this.myCharms["SmallCharms"].length > 0 && !this.dontPick.includes(pickList[0].gid) && pickList[0].itemType === 82) pickItem = true;
				
				if(pickItem) {
					while (getDistance(me, pickList[0]) > (Config.FastPick === 2 && i < 1 ? 6 : 4) || checkCollision(me, pickList[0], 0x1)) {
						if (Pather.useTeleport()) {
							Pather.moveToUnit(pickList[0]);
							delay(me.ping||100);
						} else {
							Pather.moveTo(pickList[0].x, pickList[0].y, 0);
							delay(me.ping||100);
						}
					}
					sendPacket(1, 0x16, 4, 0x4, 4, pickList[0].gid, 4, 0);
				}
			}
			
			pickList.shift();
			delay(me.ping||200);
			while (!me.idle) delay(100);
		}
	},
	
	pickItems: function() {
		while (!me.idle) {
			delay(40);
		}
		
		var item = getUnit(4),
			pickList = [];
		if (item) {
			do {
				if ((item.mode === 3 || item.mode === 5) && getDistance(me, item) <= 60) {
					pickList.push(copyUnit(item));
				}
			} while (item.getNext());
		}
		
		while (pickList.length > 0) {
			if (copyUnit(pickList[0]).x !== undefined && (pickList[0].mode === 3 || pickList[0].mode === 5) && (Pather.useTeleport() || me.inTown || !checkCollision(me, pickList[0], 0x1))) {
				var goodItem = false;
				me.overhead("Checking Item [" + pickList[0].name + "]");
				
				for(var it in this.myNeededItems) {
					var matchedAll = true;
					if("Name" in this.myNeededItems[it]) {
						if(pickList[0].fname.indexOf(this.myNeededItems[it]["Name"], 0) == -1) {
							matchedAll = false;
							continue;
						}
					}
					
					if("Type" in this.myNeededItems[it] && this.myNeededItems[it]["Type"] !== pickList[0].itemType) {
						matchedAll = false;
						continue;
					}
					
					if("Class" in this.myNeededItems[it] && this.myNeededItems[it]["Class"] !== pickList[0].itemclass) {
						matchedAll = false;
						continue;
					}
					
					if("Quality" in this.myNeededItems[it] && this.myNeededItems[it]["Quality"] !== pickList[0].quality) {
						matchedAll = false;
						continue;
					}
					
					if(matchedAll) {
						goodItem = true;
						break;
					}
				}
				
				if (goodItem && Pickit.canPick(pickList[0])) {
					var canFit = Storage.Inventory.CanFit(pickList[0]) || [4, 22, 76, 77, 78].indexOf(pickList[0].itemType) > -1;
					if (!canFit) {
						D2Bot.printToConsole("GSEquip: Error can not fit item in inventory.");
						return false;
					} else {
						print("Picking Item [" + pickList[0].name + "]");
						me.overhead("Picking Item [" + pickList[0].name + "]");
						while (getDistance(me, pickList[0]) > (Config.FastPick === 2 && i < 1 ? 6 : 4) || checkCollision(me, pickList[0], 0x1)) {
							if (Pather.useTeleport()) {
								Pather.moveToUnit(pickList[0]);
								delay(me.ping||100);
							} else {
								Pather.moveTo(pickList[0].x, pickList[0].y, 0);
								delay(me.ping||100);
							}
						}
						sendPacket(1, 0x16, 4, 0x4, 4, pickList[0].gid, 4, 0);
						
						this.toEquip.push(md5(pickList[0].name + "-" + pickList[0].itemType + "-" + pickList[0].quality));
					}
				}
			}
			pickList.shift();
			delay(me.ping||200);
			while (!me.idle) delay(100);
		}
		
		return true;
	},
	
	packetMerc: function(bytes) {
		switch(bytes[0]) {
			case 0x4e:
				var id = (bytes[2] << 8) + bytes[1];
				Mercs.push(id);
				break;
		}
	},
	
	hireMerc: function() {
		var merc = me.getMerc();
		if(merc.getSkill(114, 1)) {
			return true;
		}
		
		Mercs = [];
		addEventListener("gamepacket", this.packetMerc);
		Town.goToTown(2);
		
		var validMerc = false;
		
		while(!Town.initNPC("Merc", "newMerc")) delay(500);
		var hire = getUnit(1, Town.tasks[1].Merc);
		delay(2000);
		while(!validMerc) {
			Misc.useMenu(0x0D45);
			delay(2000);
			if(Mercs.length > 0) {
				sendPacket(1, 0x36, 4, hire.gid, 4, Mercs.pop());
				delay(2000 + (me.ping||60) * 2);
				merc = me.getMerc();
				if(merc) {
					if(merc.getSkill(114, 1)) {
						validMerc = true;
						print("New merc was hired.");
						D2Bot.printToConsole("New merc was hired.");
						removeEventListener("gamepacket", this.packetMerc);
						return true;
					}
				}
			} else {
				break;
			}
			
		}
		
		removeEventListener("gamepacket", this.packetMerc);
		
		me.cancel();
		me.cancel();
		me.cancel();
		return false;
	},
	
	farmShenk: function() {
		if(!me.getQuest(35,0)) {
			Pather.journeyTo(110);
			Pather.moveTo(3883, 5113, 15, true, true);
			var shenk = getUnit(1, 760);
			if(Attack.canAttack(shenk)) {
				Attack.kill(shenk);
				Town.goToTown();
				Town.move("Larzuk");
				var unit = getUnit(1, "Larzuk");
				if(NPC && NPC.openMenu()) { 
					me.cancel();
				}
			}
		}
	},
	
}

GSEquip.init();