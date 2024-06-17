<?php

require_once "../storedropper.diablo2store.com/config.php";

$storeID = $_GET['i'];
if($storeID != 1 AND $storeID != 0)
	die("Invalid!");

$sRealm = $_GET['r']; //1 = east
if($sRealm != 1 AND $sRealm != 2)
	die("Invalid!!");

$sHarcore = $_GET['h'];
if($sHarcore != 1 AND $sHarcore != 0)
	die("Invalid!!!");

$sLadder = $_GET['l'];
if($sLadder != 1 AND $sLadder != 0)
	die("Invalid!!!!");

$sExp = $_GET['e'];
if($sExp != 1 AND $sExp != 0)
	die("Invalid!!!!!");

$list = $_GET['list'];

$itemListsQ = $mysqli->query("SELECT * FROM item_lists WHERE il_code='" . $mysqli->real_escape_string(strtolower($list)) . "' LIMIT 0,1");
if(!$itemListsQ)
	die("Error!!");
$itemLists = $itemListsQ->fetch_assoc();

$itemGroupsQ = $mysqli->query("SELECT * FROM item_groups WHERE ig_il_id='" . $mysqli->real_escape_string($itemLists['il_id']) . "'");
if(!$itemGroupsQ)
	die("Error!!!");

if(!file_exists('../d2storesItemDBs/'.$storeID.'ItemDB.s3db'))
	die("Error!!!!");

///
///
///

$headerFont = 'fonts/Amatic-Bold.ttf';
$headerColor = explode(",", hex2rgb('DC7633'));
$headerSize = 36;

$normalFont = 'fonts/PTC75F.ttf';
$normalColor = explode(",", hex2rgb('FF5733'));
$normalSize = 14;

$priceFont = 'fonts/Sansation-Bold.ttf';
$priceFontB = 'fonts/Sansation-Regular.ttf';
$priceColor = explode(",", hex2rgb('FFC300'));
$priceSize = 16;

$backgroundColor = explode(",", hex2rgb('000000'));
$backgroundWidth = 690;
$backgroundHeight = 1700;
if($list == "keys" || $list == "torch")
	$backgroundHeight = 750;
elseif($list == "charms")
	$backgroundHeight = 1550;

$lineColor = explode(",", hex2rgb('DC7633'));

$image = @imagecreate($backgroundWidth + 10, $backgroundHeight) or die("Cannot Initialize new GD image stream");
$imageBGColor = imagecolorallocate($image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
$imageLineColor = imagecolorallocate($image, $lineColor[0], $lineColor[1], $lineColor[2]);

$runY = 45;
//Header text
$runY = $runY + makeTextBlock($headerSize, $headerFont, $headerColor, $itemLists['il_name'], 35, 0, true);

//Intro text
$runY = $runY + makeTextBlock($normalSize, $normalFont, $normalColor, "Welcome to my own 24/7 automated shop. Here you can buy items even when I am not online by sending me a simple message when you send your forum gold for the item(s).", $runY, 10, true) - 40;
//Message format
$runY = $runY + makeTextBlock($priceSize, $priceFont, $priceColor, "Message Format: BUY/ESCL/ITEM CODE/GAME NAME/GAME PASS", $runY, 0, true) - 170;
$runY = $runY + makeTextBlock($normalSize, $normalFont, $normalColor, "Include the above message in the Optional Comments section when sending your gold. Make sure you replace ITEM CODE with the item code for the item (example: BER or TAL), and also double check your game name and password. Make sure your game is in normal mode with no restrictions on level or players before sending. If you have any issues send me a message.", $runY, 10, true);
///////
$itemStart = $runY - 220;
imagelinethick($image, 0, $itemStart, $backgroundWidth + 10, $itemStart, $imageLineColor, 2);
$itemStart = $itemStart + 10;

$boxHeight = 120;
$boxWidth = ($backgroundWidth + 10) / 3;
$boxInnerWidth = $boxWidth - 14;
$boxInnerHeight = $boxHeight - 14;

$conn = new PDO('sqlite:../d2storesItemDBs/'.$storeID.'ItemDB.s3db') or die("Unable to connect");
$tempA	= " AND charHardcore = ".$sHarcore;
$tempB 	= " AND charExpansion = ".$sExp;
$tempC 	= " AND charLadder = ".$sLadder;
$sql = /** @lang text */
	'SELECT * FROM muleItems LEFT JOIN muleChars ON itemCharId = charId LEFT JOIN muleAccounts ON charAccountId = accountId WHERE accountRealm = '.$sRealm.' '.$tempA.' '.$tempB.' '.$tempC;
$results = $conn->query($sql);
if(!$results){
	print_r($conn->errorInfo());
	exit;
}
$itemsDB = $results->fetchAll(PDO::FETCH_ASSOC);

$myItems = array();

while($itemGroup = $itemGroupsQ->fetch_assoc()) {
	$myItems[$itemGroup['ig_id']] = array();
	$myItems[$itemGroup['ig_id']]['name'] = $itemGroup['ig_name'];
	$myItems[$itemGroup['ig_id']]['short'] = $itemGroup['ig_short'];
	$myItems[$itemGroup['ig_id']]['price'] = $itemGroup['ig_price_fg'];
	$myItems[$itemGroup['ig_id']]['count'] = 0;
	
	$f = 0;
	foreach($itemsDB as $item) {
		if(strtolower($item['itemName']) == strtolower($myItems[$itemGroup['ig_id']]['name'])){
			$f++;
			if($f >= 101)
				break;
		}
	}
	if($f > 0)
		$myItems[$itemGroup['ig_id']]['count'] = $f;
}

$onBox = 1;
foreach($myItems as $item) {
	
	$nName = $item['name'];
	if(strpos($nName, 'Grand Charm') !== false) {
		$nName = str_replace("Grand Charm", "GC", $nName);
	}
	
	if(strpos($nName, 'Large Charm') !== false) {
		$nName = str_replace("Large Charm", "LC", $nName);
	}
	
	if(strpos($nName, 'Small Charm') !== false) {
		$nName = str_replace("Small Charm", "SC", $nName);
	}
	
	if($item['count'] == 0)
		continue;
	if($onBox == 1) {
		//Box 1
		imagelinethick($image, 7, $itemStart, $boxInnerWidth, $itemStart, $imageLineColor, 1); //Box 1 Top
		imagelinethick($image, 7, $itemStart + $boxInnerHeight, $boxInnerWidth, $itemStart + $boxInnerHeight, $imageLineColor, 1); //Box 1 Bottom
		imagelinethick($image, 7, $itemStart, 7, $itemStart + $boxInnerHeight, $imageLineColor, 1); //Box 1 Left
		imagelinethick($image, $boxInnerWidth, $itemStart, $boxInnerWidth, $itemStart + $boxInnerHeight, $imageLineColor, 1); //Box 1 Right

		$text = $nName;
		$bounding_box_size = imagettfbbox($priceSize, 0, $priceFontB, $text);
		$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
		$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
		$priceImageColor = imagecolorallocate($image, $priceColor[0], $priceColor[1], $priceColor[2]);		
		imagettftext($image, $priceSize, 0, 14, $itemStart + 12 + abs($textHeight), $priceImageColor, $priceFontB, $text);

		$lastPriceHeight = $itemStart + 12 + abs($textHeight);

		$text = "Code: " . strtoupper($item['short']);
		$bounding_box_size = imagettfbbox($priceSize, 0, $priceFontB, $text);
		$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
		$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
		$priceImageColor = imagecolorallocate($image, $priceColor[0], $priceColor[1], $priceColor[2]);		
		imagettftext($image, $priceSize, 0, 14, $lastPriceHeight + 14 + abs($textHeight), $priceImageColor, $priceFontB, $text);

		$lastPriceHeight = $lastPriceHeight + 12 + abs($textHeight);

		$tc = $item['count'];
		if($tc > 99)
			$tc = "99+";
		$text = $item['price'] . "fg | Qty: " . $tc;
		$bounding_box_size = imagettfbbox($priceSize, 0, $priceFontB, $text);
		$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
		$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
		$priceImageColor = imagecolorallocate($image, $priceColor[0], $priceColor[1], $priceColor[2]);		
		imagettftext($image, $priceSize, 0, 14, $lastPriceHeight + 14 + abs($textHeight), $priceImageColor, $priceFontB, $text);
	}
	elseif($onBox == 2) {
		//Box 2
		imagelinethick($image, $boxWidth + 7, $itemStart, $boxWidth + 7 + $boxInnerWidth, $itemStart, $imageLineColor, 1); //Box 2 Top
		imagelinethick($image, $boxWidth + 7, $itemStart + $boxInnerHeight, $boxWidth + 7 + $boxInnerWidth, $itemStart + $boxInnerHeight, $imageLineColor, 1); //Box 2 Bottom
		imagelinethick($image, $boxWidth + 7, $itemStart, $boxWidth + 7, $itemStart + $boxInnerHeight, $imageLineColor, 1); //Box 2 Left
		imagelinethick($image, $boxWidth + 7 + $boxInnerWidth, $itemStart, $boxWidth + 7 + $boxInnerWidth, $itemStart + $boxInnerHeight, $imageLineColor, 1); //Box 2 Right

		$text = $nName;
		$bounding_box_size = imagettfbbox($priceSize, 0, $priceFontB, $text);
		$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
		$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
		$priceImageColor = imagecolorallocate($image, $priceColor[0], $priceColor[1], $priceColor[2]);		
		imagettftext($image, $priceSize, 0, $boxWidth + 14, $itemStart + 12 + abs($textHeight), $priceImageColor, $priceFontB, $text);

		$lastPriceHeight = $itemStart + 12 + abs($textHeight);

		$text = "Code: " . strtoupper($item['short']);
		$bounding_box_size = imagettfbbox($priceSize, 0, $priceFontB, $text);
		$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
		$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
		$priceImageColor = imagecolorallocate($image, $priceColor[0], $priceColor[1], $priceColor[2]);		
		imagettftext($image, $priceSize, 0, $boxWidth + 14, $lastPriceHeight + 14 + abs($textHeight), $priceImageColor, $priceFontB, $text);

		$lastPriceHeight = $lastPriceHeight + 12 + abs($textHeight);

		$tc = $item['count'];
		if($tc > 99)
			$tc = "99+";
		$text = $item['price'] . "fg | Qty: " . $tc;
		$bounding_box_size = imagettfbbox($priceSize, 0, $priceFontB, $text);
		$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
		$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
		$priceImageColor = imagecolorallocate($image, $priceColor[0], $priceColor[1], $priceColor[2]);		
		imagettftext($image, $priceSize, 0, $boxWidth + 14, $lastPriceHeight + 14 + abs($textHeight), $priceImageColor, $priceFontB, $text);
	}
	elseif($onBox == 3) {
		//Box 3
		imagelinethick($image, ($boxWidth * 2) + 7, $itemStart, ($boxWidth * 2) + 7 + $boxInnerWidth, $itemStart, $imageLineColor, 1); //Box 3 Top
		imagelinethick($image, ($boxWidth * 2) + 7, $itemStart + $boxInnerHeight, ($boxWidth * 2) + 7 + $boxInnerWidth, $itemStart + $boxInnerHeight, $imageLineColor, 1); //Box 3 Bottom
		imagelinethick($image, ($boxWidth * 2) + 7, $itemStart, ($boxWidth * 2) + 7, $itemStart + $boxInnerHeight, $imageLineColor, 1); //Box 3 Left
		imagelinethick($image, ($boxWidth * 2) + 7 + $boxInnerWidth, $itemStart, ($boxWidth * 2) + 7 + $boxInnerWidth, $itemStart + $boxInnerHeight, $imageLineColor, 1); //Box 3 Right

		$text = $nName;
		$bounding_box_size = imagettfbbox($priceSize, 0, $priceFontB, $text);
		$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
		$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
		$priceImageColor = imagecolorallocate($image, $priceColor[0], $priceColor[1], $priceColor[2]);		
		imagettftext($image, $priceSize, 0, ($boxWidth * 2) + 14, $itemStart + 12 + abs($textHeight), $priceImageColor, $priceFontB, $text);

		$lastPriceHeight = $itemStart + 12 + abs($textHeight);

		$text = "Code: " . strtoupper($item['short']);
		$bounding_box_size = imagettfbbox($priceSize, 0, $priceFontB, $text);
		$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
		$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
		$priceImageColor = imagecolorallocate($image, $priceColor[0], $priceColor[1], $priceColor[2]);		
		imagettftext($image, $priceSize, 0, ($boxWidth * 2) + 14, $lastPriceHeight + 14 + abs($textHeight), $priceImageColor, $priceFontB, $text);

		$lastPriceHeight = $lastPriceHeight + 12 + abs($textHeight);

		$tc = $item['count'];
		if($tc > 99)
			$tc = "99+";
		$text = $item['price'] . "fg | Qty: " . $tc;
		$bounding_box_size = imagettfbbox($priceSize, 0, $priceFontB, $text);
		$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
		$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
		$priceImageColor = imagecolorallocate($image, $priceColor[0], $priceColor[1], $priceColor[2]);		
		imagettftext($image, $priceSize, 0, ($boxWidth * 2) + 14, $lastPriceHeight + 14 + abs($textHeight), $priceImageColor, $priceFontB, $text);
	}
	$onBox++;
	if($onBox > 3) {
		$onBox = 1;
		$itemStart = $itemStart + $boxHeight + 14;
	}
}
if($onBox != 1)
	$itemStart = $itemStart + $boxHeight + 14;

makeTextBlock($priceSize, $priceFont, $priceColor, "Message Format: BUY/ESCL/ITEM CODE/GAME NAME/GAME PASS", $itemStart, 0, true);
makeTextBlock($normalSize, $normalFont, $normalColor, "Please read the instructions carefully its as simple as making a normal game and sending some forum gold!", $itemStart + 60, 0, true);

// Set the content type header - in this case image/png
header('Content-Type: image/png');

// Output the image
imagepng($image);

// Free up memory
imagedestroy($image);

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
    /* this way it works well only for orthogonal lines
    imagesetthickness($image, $thick);
    return imageline($image, $x1, $y1, $x2, $y2, $color);
    */
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}

function makeTextBlock($size, $font, $color, $text, $lastY = 0, $paddingY = 10, $widthCenter = true) {
	global $backgroundWidth, $image;
	
	$bounding_box_size = imagettfbbox($size, 0, $font, $text);
	$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
	$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
	$textImageColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
	
	if($textWidth >= $backgroundWidth) {
		$sText = explode(" ", $text);
		$wordsToInclude = 0;
		$lines = [];
		$cS = "";
		while($sText[$wordsToInclude] != null) {
			$cST = $cS;
			if($cST == "")
				$cST .= $sText[$wordsToInclude];
			else
				$cST .= " " . $sText[$wordsToInclude];
			$bounding_box_size = imagettfbbox($size, 0, $font, $cST);
			$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
			
			if($textWidth >= $backgroundWidth) {
				$lines[] = $cS;
				$cS = "";
			}
			else {
				$cS = $cST;
				$wordsToInclude++;
			}
		}
		if($cS != "")
			$lines[] = $cS;
		
		$lY = $lastY;
		
		foreach($lines as $line) {
			$bounding_box_size = imagettfbbox($size, 0, $font, $line);
			$textWidth = $bounding_box_size[2] - $bounding_box_size[0];
			$textHeight = $bounding_box_size[7] - $bounding_box_size[1];
			$x = 10;
			if($widthCenter)
				$x = ceil(($backgroundWidth - $textWidth) / 2);
			
			$y = $lY + $paddingY + abs($textHeight);
			imagettftext($image, $size, 0, $x, $y, $textImageColor, $font, $line);
			$lY = $lY + ($paddingY + abs($textHeight));
		}
		
		return $lY;
	} else {
		$x = 10;
		if($widthCenter)
			$x = ceil(($backgroundWidth - $textWidth) / 2);
		$y = $lastY + $paddingY + abs($textHeight);
		
		imagettftext($image, $size, 0, $x, $y, $textImageColor, $font, $text);
		return $y + $textHeight;
	}
}

// Convert color code to rgb
function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);

    switch(strlen($hex)){
        case 1:
            $hex = $hex.$hex;
        case 2:
            $r = hexdec($hex);
            $g = hexdec($hex);
            $b = hexdec($hex);
            break;
        case 3:
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
            break;
        default:
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
            break;
    }

    $rgb = array($r, $g, $b);
    return implode(",", $rgb); 
}