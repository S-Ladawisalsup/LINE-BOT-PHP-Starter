<?php
//(หนอนน้อยในพงไพร)groupId = Ca35db1d5c584c6467d717df89a0302ec
// $ggezwp = QuestionWordFromDBTB();

// echo '<table style="border: 1px solid black; border-collapse: collapse;">
// 		<thead><tr>
// 			<th style="border: 1px solid black; border-collapse: collapse;">Word</th>
// 			<th style="border: 1px solid black; border-collapse: collapse;">Type</th>
// 		</tr></thead>
// 		<tbody>';
// foreach ($ggezwp as $key) {
// 	echo '<tr><td style="border: 1px solid black; border-collapse: collapse;">' . $key['text'] . '</td>';
// 	echo '<td style="border: 1px solid black; border-collapse: collapse;">' . $key['type'] . '</td></tr>';
// }		
// echo '</tbody></table><br />';

$server_t = t_server();
echo $server_t;


function QuestionWordFromDBTB() {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$t = 'text';

	//$query = "SELECT questiontext, questiontype FROM tbhlinebotwmode WHERE questiontype = '1' OR questiontype = '7'";
	$query = "SELECT $t, type FROM tbhlinebotans WHERE type = '11'";
	$result = $db->query($query);

	$words = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $words[$index] = array();
		$words[$index]['text'] = htmlspecialchars($row["text"]);//question
		$words[$index]['type'] = htmlspecialchars($row["type"]);//question
		$index = $index + 1;
	}
	$result->closeCursor();

	return $words;
}

function t_server() {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$query = 'SELECT ip_addr FROM tbhlinebotserv ORDER BY id ASC'; 
	$result = $db->query($query);
	$hostname = array();
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$hostname['hostname'] = htmlspecialchars($row["ip_addr"]);
	}
	$result->closeCursor();

	$writer = '';
	foreach ($hostname as $host) {		
		$writer .= $host . PHP_EOL;
	}

	$file = fopen("text/server.txt", "w");
	echo fwrite($file, $writer);
	fclose($file);

	return $writer;
}