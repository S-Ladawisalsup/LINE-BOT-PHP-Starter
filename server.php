<?php

//echo "Temperature is " . $_POST["temperature"] . ' at time ' . $_POST["timestamp"];

// Get POST body content
$jsons = file_get_contents('php://input');
// Parse JSON
$datas = json_decode($jsons, true);

echo '1. data is ' . $datas["timestamp"] . '<br />';
echo '2. data is ' . $_POST["timestamp"] . '<br />';
echo '3. data is ' . $datas["data"] . '<br />';
echo '4. data is ' . $_POST["data"] . '<br />';
echo '5. json is ' . $jsons . '<br />';
echo '6. datas is ' . $datas . '<br />';
 

// //if (!is_null($datas['events'])) {
// foreach ($datas["data"] as $data) {
// 	echo 'Time is : ' . $datas["timestamp"] . '<br />';
// 	echo 'Name is : ' . $data["name"] . '<br />';
// 	echo 'res is : '. $data["name"] . '<br />';
// 	echo 'data is : '. $data["data"] . '<br />';
// }