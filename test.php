<?php

$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

$db = new PDO($dsn);

$query = 'SELECT id, ip_addr, serv_name, status, lastchangedatetime FROM tbhlinebotserv ORDER BY id ASC';
$result = $db->query($query);

echo '<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>IP Address</th>
				<th>Server Name</th>
				<th>Server Status</th>
				<th>Last Active Time</th>
			</tr>
		</thead>
		<tbody>';

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row["employee_id"] . "</td>";
    echo "<td>" . htmlspecialchars($row["last_name"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["first_name"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["title"]) . "</td>";
    echo "</tr>";
}
$result->closeCursor();

echo "</tbody></table>";