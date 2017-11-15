<?php

// $dsn = 'pgsql:'
// 		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
// 		. 'dbname=dfusod038c3j35;'
// 		. 'user=mmbbbssobrmqjs;'
// 		. 'port=5432;'
// 		. 'sslmode=require;'
// 		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

// $db = new PDO($dsn);

// $query = 'SELECT id, questiontext, questiontype, typename FROM tbhlinebotchkqa ORDER BY id ASC';
// $result = $db->query($query);

// echo '<table style="border: 1px solid black; border-collapse: collapse;">
// 		<thead>
// 			<tr>
// 				<th style="border: 1px solid black; border-collapse: collapse;">ID</th>
// 				<th style="border: 1px solid black; border-collapse: collapse;">คำถาม</th>
// 				<th style="border: 1px solid black; border-collapse: collapse;">ชนิด</th>
// 				<th style="border: 1px solid black; border-collapse: collapse;">ประเภท</th>
// 			</tr>
// 		</thead>
// 		<tbody>';

// while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
//     echo '<tr>';
//     echo '<td style="border: 1px solid black; border-collapse: collapse;">' . $row["id"] . '</td>';
//     echo '<td style="border: 1px solid black; border-collapse: collapse;">' . htmlspecialchars($row["questiontext"]) . '</td>';
//     echo '<td style="border: 1px solid black; border-collapse: collapse;">' . htmlspecialchars($row["questiontype"]) . '</td>';
//     echo '<td style="border: 1px solid black; border-collapse: collapse;">' . htmlspecialchars($row["typename"]) . '</td>';
//     echo '</tr>';
// }
// $result->closeCursor();

// echo "</tbody></table>";

// $arrayqt = getqword();
// foreach ($arrayqt as $keyitem) {
// 	echo $keyitem['text'] . '/' . $keyitem['type'] . '<br />';
// }
date_default_timezone_set("Asia/Bangkok");
echo "status 200 ok " . date("Y-m-d H:i:s");

//--------------------------------------------------------------------------------------------------------------
function getqword () {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$query = 'SELECT id, questiontext, questiontype, typename FROM tbhlinebotchkqa ORDER BY id ASC';
	$result = $db->query($query);

	$qwords = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $qwords[$index] = array();
		$qwords[$index]['text'] = htmlspecialchars($row["questiontext"]);
		$qwords[$index]['type'] = htmlspecialchars($row["questiontype"]);
		$index = $index + 1;
	}
	$result->closeCursor();

	return $qwords;
}