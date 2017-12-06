<?php
$ggezwp = QuestionWordFromDBTB();

echo '<table style="border: 1px solid black; border-collapse: collapse;">
		<thead><tr>
			<th style="border: 1px solid black; border-collapse: collapse;">Word</th>
			<th style="border: 1px solid black; border-collapse: collapse;">Type</th>
		</tr></thead>
		<tbody>';
foreach ($ggezwp as $key) {
	echo '<tr><td style="border: 1px solid black; border-collapse: collapse;">' . $key['text'] . '</td>';
	echo '<td style="border: 1px solid black; border-collapse: collapse;">' . $key['type'] . '</td></tr>';
}		
echo '</tbody></table><br />';

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

	$query = "SELECT questiontext, questiontype FROM tbhlinebotwmode WHERE questiontype = '5'";
	//$query = "SELECT $t, type FROM tbhlinebotans WHERE type = '10'";
	$result = $db->query($query);

	$words = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $words[$index] = array();
		$words[$index]['text'] = htmlspecialchars($row["questiontext"]);//
		$words[$index]['type'] = htmlspecialchars($row["questiontype"]);//
		$index = $index + 1;
	}
	$result->closeCursor();

	return $words;
}

/*
function have to add and test to line chat bot
1. Test multiple admin accepting register. 
2. Multiple push message. (maybe use for after query admin position then send message)
3. Alert admin when server status danger. (maybe use for after query admin position then send message)
4. Defind server ip in database in UI Line Chat Bot. (optional)
5. Start Greeting in group in everyday.
6. Start HBD on user that have date of birth in that day.
7. Identify user in group. 
8. Start random push message to random user(s).
*/