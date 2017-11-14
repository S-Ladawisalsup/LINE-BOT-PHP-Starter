<?php

$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

$db = new PDO($dsn);

$query = 'SELECT id, ip_addr, serv_name, status, temperature, lastchangedatetime FROM tbhlinebotserv ORDER BY id ASC';
$result = $db->query($query);

echo '<table>
		<thead>
			<tr>
				<th>ID</th>
				<th> | IP Address</th>
				<th> | Server Name</th>
				<th> | Server Status</th>
				<th> | Temperature</th>
				<th> | Last Active Time</th>
			</tr>
		</thead>
		<tbody>';

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . htmlspecialchars($row["ip_addr"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["serv_name"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["temperature"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["lastchangedatetime"]) . "</td>";
    echo "</tr>";
}
$result->closeCursor();

echo "</tbody></table>";