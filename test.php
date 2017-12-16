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
/**********************************************************************************************************************************/
/**********************************************************************************************************************************/
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

	$query = "SELECT questiontext, questiontype FROM tbhlinebotwmode WHERE questiontype = '9'";
	//$query = "SELECT $t, type FROM tbhlinebotans";// WHERE type = '14'";
	$result = $db->query($query);

	$words = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $words[$index] = array();
		$words[$index]['text'] = htmlspecialchars($row["questiontext"]);//
		$words[$index]['type'] = htmlspecialchars($row["questiontype"]);
		$index = $index + 1;
	}
	$result->closeCursor();

	return $words;
}

/*
function have to add and test to line chat bot
4. Defind server ip in database in UI Line Chat Bot. (optional)
8. Start random push message to random user(s).
8.1 add another toggle on 9am, 1pm, 3pm, 10pm to bot start push message first with random user(s). 
8.1.1 change bot_mode in tbhlinebotmodchng to relation here.
---------------------------------------------------------------------------------------------------
1. Write about notification before user register.
2. Adding position 'Dev' & defind working in line chat bot system.
---------------------------------------------------------------------------------------------------
3. Add more response word.
*/