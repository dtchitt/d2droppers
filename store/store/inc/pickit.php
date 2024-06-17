<?php

require_once "discord.php";

function PickitQual($name) {
	$quals = [
		"lowquality" => 1,
		"low" => 1,
		"normal" => 2,
		"superior" => 3,
		"magic" => 4,
		"set" => 5,
		"rare" => 6,
		"unique" => 7,
		"crafted" => 8,
		"craft" => 8
	];
	
	return $quals[strtolower($name)];
}

function findItems($line, $realm) {
	global $mysqli;
	//echo("Full Line [" . $line . "]<br>");
	$lineParts = [[],[],[]];
	
	if(strpos($line, '#') !== false) {
		$ps = explode("#", $line);
		$oc = 0;
		foreach($ps as $mainpart) {
			if(strpos($mainpart, '&&') !== false) {
				$sp = explode("&&", $mainpart);
				foreach($sp as $subpart) {
					$lineParts[$oc][] = trim($subpart);
				}
			} else {
				$lineParts[$oc][] = trim($mainpart);
			}
			$oc++;
		}
	} else {
		echo "(2)<br>";
		if(strpos($line, '&&') !== false) {
			$sp = explode("&&", $line);
			foreach($sp as $subpart) {
				$lineParts[0][] = trim($subpart);
			}
		} else {
			$lineParts[0][] = trim($line);
		}
	}
	
	$exw = " ";
	$statsToParse = [];
	if(count($lineParts[0]) > 0) {
		foreach($lineParts[0] as $lpk => $lpo) {
			echo "LPO [" . $lpo . "]<br>";
			//Equal
			$spo = [];
			$sign = "equal";
			if(strpos($lpo, '==') !== false) {
				$spo = explode("==", $lpo);
				$sign = "equal";
			} else if(strpos($lpo, '>=') !== false) {
				$spo = explode(">=", $lpo);
				$sign = "greaterequal";
			} else if(strpos($lpo, '<=') !== false) {
				$spo = explode("<=", $lpo);
				$sign = "equalless";
			} else if(strpos($lpo, '>') !== false) {
				$spo = explode(">", $lpo);
				$sign = "greater";
			} else if(strpos($lpo, '<') !== false) {
				$spo = explode("<", $lpo);
				$sign = "less";
			} else if(strpos($lpo, '!=') !== false) {
				$spo = explode("!=", $lpo);
				$sign = "notequal";
			}
			
			if(strpos($spo[0], '+') !== false) { //multi params
				$props = explode("+", $spo[0]);
				$ta = [];
				foreach($props as $prop) {
					$ta[] = trim(str_replace(["[", "]"], "", $prop));
				}
				$statsToParse[] = [$ta, $sign, $spo[1]];
			} else {
				$spo[0] = trim(str_replace(["[", "]"], "", $spo[0]));
				$spo[1] = trim($spo[1]);
				//echo("Adding Stat To Parse [" . $spo[0] . "] [" . $sign . "] [" . $spo[1] . "]<br>");
				$statsToParse[] = [[$spo[0]], $sign, $spo[1]];
			}
		}
	}
	
	if(count($lineParts[1]) > 0) {
		foreach($lineParts[1] as $lpk => $lpo) {
			//Equal
			$spo = [];
			$sign = "equal";
			if(strpos($lpo, '==') !== false) {
				$spo = explode("==", $lpo);
				$sign = "equal";
			} else if(strpos($lpo, '>=') !== false) {
				$spo = explode(">=", $lpo);
				$sign = "greaterequal";
			} else if(strpos($lpo, '<=') !== false) {
				$spo = explode("<=", $lpo);
				$sign = "equalless";
			} else if(strpos($lpo, '>') !== false) {
				$spo = explode(">", $lpo);
				$sign = "greater";
			} else if(strpos($lpo, '<') !== false) {
				$spo = explode("<", $lpo);
				$sign = "less";
			} else if(strpos($lpo, '!=') !== false) {
				$spo = explode("!=", $lpo);
				$sign = "notequal";
			}
			
			if(strpos($spo[0], '+') !== false) { //multi params
				$props = explode("+", $spo[0]);
				$ta = [];
				foreach($props as $prop) {
					$ta[] = trim(str_replace(["[", "]"], "", $prop));
				}
				$statsToParse[] = [$ta, $sign, $spo[1]];
			} else {
				$spo[0] = trim(str_replace(["[", "]"], "", $spo[0]));
				$spo[1] = trim($spo[1]);
				echo("(1) Adding Stat To Parse [" . $spo[0] . "] [" . $sign . "] [" . $spo[1] . "]<br>");
				$statsToParse[] = [[$spo[0]], $sign, $spo[1]];
			}
		}
	}
	
	
	foreach($statsToParse as $ts) {
		if(count($ts[0]) == 1) {
			if($ts[0][0] == "quality") {
				if($ts[1] == "equal") {
					$exw .= "AND item.i_qual = '" . PickitQual($ts[2]) . "' ";
				} else if($ts[1] == "notequal") {
					$exw .= "AND item.i_qual != '" . PickitQual($ts[2]) . "' ";
				} else {
					sendDiscordCurrentStore("Error Parsing Pickit Line [" . $line . "] On Part [" . $ts[0][0] . " " . $ts[1] . " " . $ts[2] . "]");
				}
			} else if($ts[0][0] == "type") {
				if($ts[1] == "equal") {
					$exw .= "AND item.i_typ = '" . NameToTypeID($ts[2]) . "' ";
				} else if($ts[1] == "notequal") {
					$exw .= "AND item.i_typ != '" . NameToTypeID($ts[2]) . "' ";
				} else {
					sendDiscordCurrentStore("Error Parsing Pickit Line [" . $line . "] On Part [" . $ts[0][0] . " " . $ts[1] . " " . $ts[2] . "]");
				}
			}
		}
	}
	
	
	
	$sql = "SELECT acc.a_id, acc.a_store_id, acc.a_realm, acc.a_username, acc.a_password, chr.c_id, chr.c_acc_id, chr.c_name, chr.c_realm, chr.c_game_type, chr.c_player_type, chr.c_ladder, 
		chr.c_class, item.i_id, item.i_char_id, item.i_name, item.i_flags, item.i_color, item.i_image, item.i_hash, item.i_script, item.i_class, item.i_class_type, item.i_qual, item.i_typ
		FROM d2_accounts AS acc 
		INNER JOIN d2_chars AS chr
		INNER JOIN d2_items AS item
		WHERE acc.a_store_id = '1' AND 
		acc.a_realm = '" . $realm . "' AND 
		chr.c_acc_id = acc.a_id AND 
		item.i_char_id = chr.c_id" . $exw;
	
	if (!$result = $mysqli->query($sql)) {
		die($mysqli->error . "<br>" . $sql);
		return false;
	}
	
	if ($result->num_rows === 0) {
		return false;
	}
	
	print_r($statsToParse);
	
	$goodItems = [];
	
	while($row = $result->fetch_assoc()) {
		$isGood = true;
		foreach($statsToParse as $ts) {
			if($ts[0][0] == "quality" || $ts[0][0] == "type" || !$isGood)
				continue;
			
			echo "Checking item [" . $row['i_name'] . "]<br>";
			
			$ev = 0;
			if(count($ts[0]) > 1) {
				echo "st (1)<br>";
				foreach($ts[0] as $ss) {
					$ev = $ev + checkStat($row, $ss);
				}
			} else {
				if($ts[0][0] == "name") {
					$nn = strtolower(trim(str_replace([" ", "\n", "'"], "", $row['i_name'])));
					if($ts[1] == "equal") {
						if($ts[2] != $nn && strpos($nn, $ts[2]) === false) {
							$isGood = false;
							continue;
						}
					}
					continue;
				} elseif($ts[0][0] == "flag" && $ts[2] == "ethereal") {
					echo("eth check<br>");
					if($ts[1] == "equal") {
						if(strpos($row['i_script'], 'Ethereal (Cannot be Repaired)') === false) {
							$isGood = false;
							continue;
						} else {
							continue;
						}
					} elseif($ts[1] == "notequal") {
						print("Looking for non eth<br>" . $row['i_script'] . "<br>");
						if(strpos($row['i_script'], 'Ethereal (Cannot be Repaired)') !== false) {
							//print("Found eth tag.<br>");
							$isGood = false;
							continue;
						} else {
							print("Were good not eth.<br>");
							continue;
						}
					}
				} elseif($ts[0][0] == "flag" && $ts[2] == "identified") {
					echo("id check<br>");
					if($ts[1] == "equal") {
						if(strpos($row['i_script'], 'Unidentified') === false) {
							$isGood = false;
							continue;
						} else {
							continue;
						}
					} elseif($ts[1] == "notequal") {
						print("Looking for non id<br>" . $row['i_script'] . "<br>");
						if(strpos($row['i_script'], 'Unidentified') !== false) {
							//print("Found eth tag.<br>");
							$isGood = false;
							continue;
						} else {
							print("Were good not idd.<br>");
							continue;
						}
					}
				} else {
					$ev = $ev + checkStat($row, $ts[0][0]);
				}
			}
			
			if($ts[1] == "equal") {
				if($ev != $ts[2]) {
					echo "STAT LINE FAILED equal [" . $ts[0][0] . " " . $ts[1] . " " . $ts[2] . "]<br>";
					$isGood = false;
					continue;
				}
			} else if($ts[1] == "greaterequal") {
				if($ev < $ts[2]) {
					echo "STAT LINE FAILED equal [" . $ts[0][0] . " " . $ts[1] . " " . $ts[2] . "]<br>";
					$isGood = false;
					continue;
				}
			} else if($ts[1] == "lessequal") {
				if($ev > $ts[2]) {
					echo "STAT LINE FAILED equal [" . $ts[0][0] . " " . $ts[1] . " " . $ts[2] . "]<br>";
					$isGood = false;
					continue;
				}
			} else if($ts[1] == "greater") {
				if($ev <= $ts[2]) {
					echo "STAT LINE FAILED equal [" . $ts[0][0] . " " . $ts[1] . " " . $ts[2] . "]<br>";
					$isGood = false;
					continue;
				}
			} else if($ts[1] == "less") {
				if($ev >= $ts[2]) {
					echo "STAT LINE FAILED equal [" . $ts[0][0] . " " . $ts[1] . " " . $ts[2] . "]<br>";
					$isGood = false;
					continue;
				}
			} else if($ts[1] == "notequal") {
				if($ev == $ts[2]) {
					echo "STAT LINE FAILED equal [" . $ts[0][0] . " " . $ts[1] . " " . $ts[2] . "]<br>";
					$isGood = false;
					continue;
				}
			}
		}
		if($isGood) $goodItems[] = $row;
	}
	
	//echo "<br><br>Total Items: " . $result->num_rows . "<br>";
	//echo "Filtered Res: " . count($goodItems) . "<br>";
	
	//foreach($goodItems as $g) {
	//	echo("Item Valid [" . $g['i_name'] . "]<br>");
	//}
	return $goodItems;
}

function checkStat($item, $stat) {
	$stats = [
		"fireskilltab" => '/\+([0-9]) to Fire Skills \(Sorceress Only\)/i',
		"coldskilltab" => '/\+([0-9]) to Cold Skills \(Sorceress Only\)/i',
		"lightningskilltab" => '/\+([0-9]) to Lightning Skills \(Sorceress Only\)/i',
		
		"fcr" => '/\+([1-9][0-9]|[0-9])\% Faster Cast Rate/i',
		
		"itemopenwounds" => '/([1-9][0-9][0-9]|[1-9][0-9]|[1-9])\% Chance Of Open Wounds/i',
		
		"allstats" => '/\+([1-9][0-9][0-9]|[1-9][0-9]|[1-9]) to All Attributes/i',
		"strength" => '/\+([1-9][0-9]|[1-9]) to Strength/i',
		"dexterity" => '/\+([1-9][0-9]|[1-9]) to Dexterity/i',
		"maxhp" => '/\+([1-9][0-9]|[1-9]) to Life/i',
		"itemmaxmanapercent" => '/Increase Maximum Mana ([1-9][0-9]|[1-9])\%/i',
		
		"itemmagicbonus" => '/([1-9][0-9][0-9]|[1-9][0-9]|[1-9])\% Better Chance of Getting Magic Items/i',
		"itemgoldbonus" => '/([1-9][0-9][0-9]|[1-9][0-9]|[1-9])\% Extra Gold From Monsters/i',
		
		"itemabsorblightpercent" => '/Lightning Absorb ([1-9][0-9]|[1-9])\%/i',
		"magicdamagereduction" => '/Magic Damage Reduced by ([1-9][0-9]|[1-9])/i',
		"lightresist" => '/Lightning Resist \+([1-9][0-9][0-9]|[1-9][0-9]|[1-9])\%/i',
		"fireresist" => '/Fire Resist \+([1-9][0-9][0-9]|[1-9][0-9]|[1-9])\%/i',
		"allres" => '/All Resistances \+([1-9][0-9][0-9]|[1-9][0-9]|[1-9])/i',
		
		"maxstamina" => '/\+([1-9][0-9]|[1-9]) Maximum Stamina/i',
		
		"lifeleech" => '/([1-9][0-9]|[1-9])\% Life stolen per hit/i',
		"manaleech" => '/([1-9][0-9]|[1-9])\% Mana stolen per hit/i',
		"hpregen" => '/Replenish Life \+([1-9][0-9]|[1-9])/i',
		"manaregen" => '/Replenish Life \+([1-9][0-9]|[1-9])/i',
		
		"tohit" => '/\+([1-9][0-9][0-9]|[1-9][0-9]|[1-9]) to Attack Rating/i',
		
		"enhanceddefense" => '/\+([1-9][0-9][0-9]|[1-9][0-9]|[1-9])\% Enhanced Defense/i',
		"plusdefense" => '/\+([1-9][0-9][0-9]|[1-9][0-9]|[1-9]) Defense/i',
		"defense" => '/Defense\: ([1-9][0-9][0-9][0-9]|[1-9][0-9][0-9]|[1-9][0-9]|[1-9])/i',
		
	];
	/*
	itemaddclassskills, maxhp, coldresist, fireresist, lightresist, poisonresist, maxmana, tohit, mindamage, maxdamage, toblock,
	frw, fhr, ias, itemknockback, itemdeadlystrike, sockets, itemtohitpercentperlevel, palicombatskilltab, shadowdisciplinesskilltab, maxstamina, lifeleech,
	itemmagicbonus, itemabsorblightpercent, magicdamagereduction, necromancerskills, paladinskills, assassinskills, amazonskills, druidskills, assassinskills,
	barbarianskills, warcriesskilltab, itemgoldbonus, poisonandboneskilltab, sorceressskills, normaldamagereduction, itemmaxdurabilitypercent,
	passivecoldmastery, skillblizzard, passivefiremastery, skillfireball, passiveltngmastery, skilllightning, skillnova, skillenergyshield, itemallskills, damageresist,
	itemreplenishdurability, itemreqpercent, javelinandspearskilltab, passiveandmagicskilltab, enhanceddamage, skillblessedhammer, skillconcentration, skillfistoftheheavens,
	skillconviction, skillholyshield, skillchillingarmor, skillshiverarmor, sanctuaryaura, skillbonespear, skillbonespirit, skillpoisonnova, bowandcrossbowskilltab, skilllightningsentry,
	skillweaponblock, trapsskilltab, skillvenom, skillfade, skillwerewolf, skilllycanthropy, skillferalrage, vitality, skillbattleorders, skillfiremastery, skillcoldmastery,
	skilllightningmastery, shapeshiftingskilltab, skilltornado, skillarmageddon, skillsummongrizzly, skillfury, elementalskilltab, fbr, passivecoldpierce, passivepoispierce,
	passivepoismastery, passiveltngpierce, passivefirepierce, coldmindam, coldmaxdam, lightmaxdam, lightmindam, firemindam, firemaxdam
	*/
	if($stats[strtolower(trim($stat))]) {
		$matches = [];
		preg_match($stats[strtolower(trim($stat))], $item['i_script'], $matches, PREG_OFFSET_CAPTURE, 0);
		if(count($matches) > 0) {
			return $matches[1][0];
		} else {
			return 0;
		}
	} else {
		if(strlen($stat) > 0) {
			echo "Unknown Stat [" . $stat . "]<br>";
			sendDiscordCurrentStore("Missing Stat [" . $stat . "].");
		}
		return 0;
	}
	return 0;
}