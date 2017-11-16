<?php

$result = $_POST["temperature"];

echo "Temperature is " . $result . '<br />';

// Get POST body content
$jsons = file_get_contents('php://input');
// Parse JSON
$datas = json_decode($jsons, true);

if (!is_null($_POST["temperature"]) || !empty($_POST['temperature'])) {
//if (!empty($_POST['temperature'])) {
	echo "Temperature is " . $_POST["temperature"];// . ' at time ' . $_POST["timestamp"];
}
else {
	echo "Cannot receive any temperature data";
}
echo '<br />';

// echo '1. data is ' . $datas["timestamp"] . '<br />';
// echo '2. data is ' . $_POST["timestamp"] . '<br />';
// echo '3. data is ' . $datas["data"] . '<br />';
// echo '4. data is ' . $_POST["data"] . '<br />';
// echo '5. json is ' . $jsons . '<br />';
// echo '6. datas is ' . $datas;

if (!is_null($datas['events'])) {
// foreach ($datas["data"] as $data) {
	echo 'Time is : ' . $datas["timestamp"] . '<br />';
	echo 'Name is : ' . $datas["data"]["name"] . '<br />';
	echo 'res is : ' . $datas["data"]["res"] . '<br />';
	echo 'data is : ' . $datas["data"]["data"];
}
else {
	echo "Cannot receive any json data";
}