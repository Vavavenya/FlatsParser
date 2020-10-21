<?php
include 'dbConnection.php';

$configSettings = json_decode(file_get_contents('config.json'), true);
$currentPageNumber = $configSettings['startPage']??'0';

while ($currentPageNumber<$configSettings['amountPage']){
	$url = 'https://realt.by/rent/flat-for-day/?page='.$currentPageNumber;
	$opts = [
		'http'=>[
			'method'=>"GET",
			'header'=> "User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36\r\n"
		]
	];

	$context = stream_context_create($opts);
	$internalErrors = libxml_use_internal_errors(true);

	$dom = new DOMDocument;
	$dom->loadHTML(file_get_contents($url, false, $context));
	$xPath = new DOMXPath($dom);

	$idList = getAllFlatsId($databaseLink);

	$divs = $xPath->query('//div');
	foreach ($divs as $div) {
		$className = $xPath->evaluate('string(./@class)', $div);
		if($className === 'bd-item ') {
			$adId = substr(getChildContentByClassName($xPath, $div, 'fr f11 grey'), 1);
			if (in_array($adId, $idList)){
				continue;
			}
			$adTitle = getChildContentByClassName($xPath, $div, 'media-body');
			$adCost = getChildContentByClassName($xPath, $div, 'price-byr');
			preg_match("/^\d+/", $adCost, $adCost);
			$adCost = $adCost[0];
			$adDate = date('d.m.y', strtotime(getChildContentByClassName($xPath, $div, 'fl f11 grey')));
			$adDescription = getChildContentByClassName($xPath, $div, 'bd-item-right-center');
			$adImage = getChildContentByClassName($xPath, $div, 'lazy', 'data-original');
			$adPhoneNumber = getChildNumberByClassName($xPath, $div, 'mb0', 'data-full');

			$sql = sprintf("INSERT INTO ad_flats VALUES (%d,'%s',%d,'%s','%s','%s','%s')", $adId, $adTitle, $adCost, $adDate, $adDescription, $adImage, $adPhoneNumber);

			$result = mysqli_query($databaseLink, $sql) or die("Ошибка " . mysqli_error($databaseLink));
		}
	}
	$currentPageNumber++;
}

function getChildContentByClassName ($xPath,$childDoc, $className, $attributeName = null){
	$textChildren = $xPath->query("descendant::*[@class='$className']", $childDoc);
	foreach ($textChildren as $node) {
		return $attributeName?$node->getAttribute($attributeName):trim($node->nodeValue);
	}

	return null;
}

function getChildNumberByClassName ($xPath,$childDoc, $className, $childAttribute){
	$textChildren = $xPath->query("descendant::*[@class='$className']", $childDoc);
	foreach ($textChildren as $node) {
		return $node->firstChild->getAttribute($childAttribute);
	}

	return null;
}
header("Location: /index.php");

