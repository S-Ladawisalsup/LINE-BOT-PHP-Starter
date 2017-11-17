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


// $db = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=admin123");
// $result = pg_query($db, "SELECT * FROM book where book_id = '$_POST[bookid]'");
// $row = pg_fetch_assoc($result);

// if (isset($_POST['submit'])) {
// 	echo "<ul><form name='update' action='enter-bookid.php' method='POST' >
// 	<li>Book ID:</li>
// 	<li><input type='text' name='bookid_updated' value='$row[book_id]' /></li>
// 	<li>Book Name:</li>
// 	<li><input type='text' name='book_name_updated' value='$row[name]' /></li>
// 	<li>Price (USD):</li><li><input type='text' name='price_updated' value='$row[price]' /></li>
// 	<li>Date of publication:</li>
// 	<li><input type='text' name='dop_updated' value='$row[date_of_publication]' /></li>
// 	<li><input type='submit' name='new' /></li>
// 	</form>
// 	</ul>";
// }

// if (isset($_POST['new'])) {
// 	$result = pg_query($db, "UPDATE book SET book_id = $_POST[bookid_updated],
// 	name = '$_POST[book_name_updated]',price = $_POST[price_updated],
// 	date_of_publication = $_POST[dop_updated]");
// 	if (!$result){
// 		echo "Update failed!!";
// 	}
// 	else
// 	{
// 		echo "Update successfull;";
// 	} 
// }


include 'utilities.php';
$q4 = QuestionWordFromDB();
// foreach ($q4 as $key1) {
// 	echo $key1['text'] . '/' . $key1['type'] . '<br />';
// }
echo "status 400 ok " . $q4;