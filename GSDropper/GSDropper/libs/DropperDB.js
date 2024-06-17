/**
*	@filename		DropperDB.js
*	@author			azero
**/

var DropperDB = {
    ItemsToSkip: [22, 76, 77, 78, 39, 18, 39],
    DataList: {},

    init: function() {
        if (!FileTools.exists("DropperDB")) {
            var folder = dopen("");
            folder.create("DropperDB");
        }

        if (!FileTools.exists("DropperDB/Out")) {
            var folder = dopen("");
            folder.create("DropperDB/Out");
        }
		
		if (!FileTools.exists("DropperDB/In")) {
            var folder = dopen("");
            folder.create("DropperDB/In");
        }

        this.DataList.Account = {
            User: me.account.toLowerCase(),
            Pass: this.getAccountPassword(),
            Char: me.name,
            GameType: me.gametype,
            PlayerType: me.playertype,
            Ladder: me.ladder,
            ClassID: me.classid,
            Realm: me.realm.toLowerCase()
        };

        this.DataList.Items = [];
        var items = me.getItems();

        for(var i = 0; i < items.length; i += 1) {
            if(items[i] && this.ItemsToSkip.indexOf(items[i].itemType) === -1 && items[i].mode !== 1) {
				var itemStats = this.getItemStats(items[i]);
                this.DataList.Items.push({
                    Name: this.filterItemName(items[i].fname),
                    Flags: items[i].getFlags(),
                    Color: items[i].getColor(),
                    Image: this.getItemImage(items[i]),
                    Hash: md5(items[i].fname + items[i].description + this.getItemImage(items[i])),
                    Description: this.getItemDesc(items[i]),
                    Location: [items[i].x, items[i].y],
                    Stats: itemStats,
					ItemType: items[i].itemType,
					ItemClass: items[i].itemclass,
					ItemClassID: items[i].classid,
					ItemQuality: items[i].quality,
				});
            }
		}

        var safeRealm = me.realm.toLowerCase();
        if(safeRealm.length < 1)
            safeRealm = "SinglePlayer";
        
        var safeAccount = me.account.toLowerCase();
        if(safeAccount.length < 1)
        safeAccount = "SinglePlayer";
            

        var jsonData = JSON.stringify(this.DataList);
        var writeTofile = File.open("DropperDB/Out/" + safeRealm + "---" + safeAccount + "---" + me.name + ".json", FILE_WRITE, false, true, true);
        writeTofile.write(jsonData);
        writeTofile.close();
        print("DropperDB Done Logging!");
        return true;
    },

    getAccountPassword: function() {
        var i;
        //Attempt to get password from Mule Logger
        if (!isIncluded("MuleLogger.js")) include("MuleLogger.js");
        for (i in MuleLogger.LogAccounts) {
			if (MuleLogger.LogAccounts.hasOwnProperty(i) && typeof i === "string") {
				for (var j in MuleLogger.LogAccounts[i]) {
					if (MuleLogger.LogAccounts[i].hasOwnProperty(j) && typeof j === "string") {
						if (j.split("/")[0].toLowerCase() === me.account.toLowerCase()) {
							return j.split("/")[1];
						}
					}					
				}
			}
        }
        
        //Attempt to get password from auto mule
        if (!isIncluded("AutoMule.js")) include("AutoMule.js");
        for (i in AutoMule.Mules) {
			if (AutoMule.Mules[i].accountPrefix) {
				if (me.account.toLowerCase().match(AutoMule.Mules[i].accountPrefix.toLowerCase())) {
					this.mulePass = AutoMule.Mules[i].accountPassword;
					return true;
				}
			}
		}
		
		for (i in AutoMule.TorchAnniMules) {
			if (AutoMule.TorchAnniMules[i].accountPrefix) {
				if (me.account.toLowerCase().match(AutoMule.TorchAnniMules[i].accountPrefix.toLowerCase())) {
					this.mulePass = AutoMule.TorchAnniMules[i].accountPassword;
					return true;
				}
			}
		}
		
		return false;
    },

    filterString: function(msg) {
        msg = msg.replace(/[\0\n\r\b\t\\'"\x1a]/g, function (s) {
			switch (s) {
				case "\0":
					return "\\0";
				case "\n":
					return "\\n";
				case "\r":
					return "\\r";
				case "\b":
					return "\\b";
				case "\t":
					return "\\t";
				case "\x1a":
					return "\\Z";
				case "'":
					return "''";
				case '"':
					return '""';
				default:
					return "\\" + s;
			}
		});
		
		return msg;
    },

    filterItemName: function(itemName) {
        return this.filterString(itemName).split("\n").reverse().join(" ").replace(/(y|ÿ)c[0-9!"+<;.*]/, "").replace(/[^\x00-\x7F]/g, "").replace("c1", "").replace("c2", "").replace("c3", "").replace("c4", "").replace("c5", "").replace("c6", "").replace("c7", "").replace("c8", "").replace("c9", "").trim();
    },

    getItemImage: function(unit) {
        var code, i;
		switch (unit.quality) {
			case 5: // Set
				switch (unit.classid) {
				case 27: // Angelic sabre
					code = "inv9sbu";

					break;
				case 74: // Arctic short war bow
					code = "invswbu";

					break;
				case 308: // Berserker's helm
					code = "invhlmu";

					break;
				case 330: // Civerb's large shield
					code = "invlrgu";

					break;
				case 31: // Cleglaw's long sword
				case 227: // Szabi's cryptic sword
					code = "invlsdu";

					break;
				case 329: // Cleglaw's small shield
					code = "invsmlu";

					break;
				case 328: // Hsaru's buckler
					code = "invbucu";

					break;
				case 306: // Infernal cap / Sander's cap
					code = "invcapu";

					break;
				case 30: // Isenhart's broad sword
					code = "invbsdu";

					break;
				case 309: // Isenhart's full helm
					code = "invfhlu";

					break;
				case 333: // Isenhart's gothic shield
					code = "invgtsu";

					break;
				case 326: // Milabrega's ancient armor
				case 442: // Immortal King's sacred armor
					code = "invaaru";

					break;
				case 331: // Milabrega's kite shield
					code = "invkitu";

					break;
				case 332: // Sigon's tower shield
					code = "invtowu";

					break;
				case 325: // Tancred's full plate mail
					code = "invfulu";

					break;
				case 3: // Tancred's military pick
					code = "invmpiu";

					break;
				case 113: // Aldur's jagged star
					code = "invmstu";

					break;
				case 234: // Bul-Kathos' colossus blade
					code = "invgsdu";

					break;
				case 372: // Grizwold's ornate plate
					code = "invxaru";

					break;
				case 366: // Heaven's cuirass
				case 215: // Heaven's reinforced mace
				case 449: // Heaven's ward
				case 426: // Heaven's spired helm
					code = "inv" + unit.code + "s";

					break;
				case 357: // Hwanin's grand crown
					code = "invxrnu";

					break;
				case 195: // Nalya's scissors suwayyah
					code = "invskru";

					break;
				case 395: // Nalya's grim helm
				case 465: // Trang-Oul's bone visage
					code = "invbhmu";

					break;
				case 261: // Naj's elder staff
					code = "invcstu";

					break;
				case 375: // Orphan's round shield
					code = "invxmlu";

					break;
				case 12: // Sander's bone wand
					code = "invbwnu";

					break;
				}

				break;
			case 7: // Unique
				for (i = 0; i < 401; i += 1) {
					if (unit.fname.split("\n").reverse()[0].indexOf(getLocaleString(getBaseStat(17, i, 2))) > -1) {
						code = getBaseStat(17, i, "invfile");

						break;
					}
				}

				break;
		}

		if (!code) {
			if (["ci2", "ci3"].indexOf(unit.code) > -1) { // Tiara/Diadem
				code = unit.code;
			} else {
				code = getBaseStat(0, unit.classid, 'normcode') || unit.code;
			}

			code = code.replace(" ", "");

			if ([10, 12, 58, 82, 83, 84].indexOf(unit.itemType) > -1) {
				code += (unit.gfx + 1);
			}
		}
		
		return code;
    },

    getItemDesc: function(item) {
        var desc = item.description,
            finalOut = [];
        if(!desc) return "";

        desc = desc.split("\n").reverse();
        for(var i = 0; i < desc.length; i += 1) {
            if (desc[i].indexOf(getLocaleString(3331)) === -1) {
                var color = 0;
				
				desc[i] = desc[i].replace("+", "__");

                if(desc[i].indexOf("ÿc0") >= 0) {
                    desc[i] = desc[i].replace("ÿc0", "");
                    color = 0;
                } else if(desc[i].indexOf("ÿc1") >= 0) {
                    desc[i] = desc[i].replace("ÿc1", "");
                    color = 1;
                } else if(desc[i].indexOf("ÿc2") >= 0) {
                    desc[i] = desc[i].replace("ÿc2", "");
                    color = 2;
                } else if(desc[i].indexOf("ÿc3") >= 0) {
                    desc[i] = desc[i].replace("ÿc3", "");
                    color = 3;
                } else if(desc[i].indexOf("ÿc4") >= 0) {
                    desc[i] = desc[i].replace("ÿc4", "");
                    color = 4;
                } else if(desc[i].indexOf("ÿc5") >= 0) {
                    desc[i] = desc[i].replace("ÿc5", "");
                    color = 5;
                } else if(desc[i].indexOf("ÿc6") >= 0) {
                    desc[i] = desc[i].replace("ÿc6", "");
                    color = 6;
                } else if(desc[i].indexOf("ÿc7") >= 0) {
                    desc[i] = desc[i].replace("ÿc7", "");
                    color = 7;
                } else if(desc[i].indexOf("ÿc8") >= 0) {
                    desc[i] = desc[i].replace("ÿc8", "");
                    color = 8;
                } else if(desc[i].indexOf("ÿc9") >= 0) {
                    desc[i] = desc[i].replace("ÿc9", "");
                    color = 9;
                }
                finalOut.push([color, desc[i]]);
            }
        }
        finalOut.push([0, "(Item Level: " + item.ilvl + ")"]);
        return finalOut;
    },

    getItemStats: function(item) {
        var val, i, n,
			stats = item.getStat(-2),
			dump = [];
		for(var o in stats) {
			if(o != "first" && o != "shuffle") {
				if(stats[o]) {
					for(var n in NTIPAliasStat) {
						if(NTIPAliasStat[n] == o) {
							dump.push([n, stats[o]]);
						}
					}
				}
			}
		}
		
		return dump;
    },
}