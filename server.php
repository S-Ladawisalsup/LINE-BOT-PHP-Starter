<?php

//echo "Temperature is " . $_POST["temperature"] . ' at time ' . $_POST["timestamp"];

// Get POST body content
$jsons = file_get_contents('php://input');
// Parse JSON
$datas = json_decode($jsons, true);

//if (!is_null($datas['events'])) {
foreach ($datas as $data) {
	echo 'Time is : ' . $data["timestamp"] . '<br />';
	echo 'Name is : ' . $data["data"]["name"] . '<br />';
	echo 'res is : '. $data["data"]["name"] . '<br />';
	echo 'data is : '. $data["data"]["data"] . '<br />';
}