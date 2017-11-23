<?php

$ggezwp = QuestionWordFromDBTB();

echo '<table style="border: 1px solid black; border-collapse: collapse;">
		<thead><tr>
			<th style="border: 1px solid black; border-collapse: collapse;>Word</th>
			<th style="border: 1px solid black; border-collapse: collapse;>Type</th>
		</tr></thead>
		<tbody>';
foreach ($ggezwp as $key) {
	echo '<tr><td style="border: 1px solid black; border-collapse: collapse;>' . $key['text'] . '</td>';
	echo '<td style="border: 1px solid black; border-collapse: collapse;>' . $key['type'] . '</td></tr>';
}		
echo '</tbody></table>';

function QuestionWordFromDBTB() {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$query = "SELECT id, questiontext, questiontype FROM tbhlinebotwmode WHERE questiontype = '8'";
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