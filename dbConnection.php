<?php
$databaseLink = mysqli_connect('localhost', 'root', 'root', 'flats');

if ($databaseLink == false){
	print('Ошибка: Невозможно подключиться к MySQL ' . mysqli_connect_error());
}

function getAllFlatsId($dbLink){
	$result = mysqli_query($dbLink, 'SELECT id FROM ad_flats') or die('Ошибка ' . mysqli_error($dbLink));
	$idList = []; 
	while ($row = mysqli_fetch_array($result)) {
		$idList[] = $row['id'];
	}

	return $idList;
}

