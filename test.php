<?php
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

$str = "อะไรบ้าง";
$str2 = "ไปที่ไหนดี";
$str3 = "HelloWorld";
echo mb_substr($str, 0, -4) . '<br />' . mb_substr($str2, 0, -2) . '<br />' . substr($str3, 0, -5);

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

	$query = "SELECT questiontext, questiontype FROM tbhlinebotwmode";//WHERE questiontype = '1'";
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
* What do I do today
* 1. substring endwiths in php language with word 'บ้าง' and 'ดี'.
* 2. change bot reply and push if id_type (user, group, room) is user's type
*    when call bot do not call bot's name.
* 3. Insert data member to postggesql database (tbhlinebotmem)
*    and try to using that data.
* 4. Create register member system to bot.
*/