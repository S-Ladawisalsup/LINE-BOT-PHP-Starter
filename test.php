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

echo '<table style="border: 1px solid black; border-collapse: collapse;">
		<thead>
			<tr>
				<th style="border: 1px solid black; border-collapse: collapse;">ID</th>
				<th style="border: 1px solid black; border-collapse: collapse;">IP Address</th>
				<th style="border: 1px solid black; border-collapse: collapse;">Server Name</th>
				<th style="border: 1px solid black; border-collapse: collapse;">Server Status</th>
				<th style="border: 1px solid black; border-collapse: collapse;">Last Active Time</th>
			</tr>
		</thead>
		<tbody>';

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
    echo '<td style="border: 1px solid black; border-collapse: collapse;">' . $row["id"] . '</td>';
    echo '<td style="border: 1px solid black; border-collapse: collapse;">' . htmlspecialchars($row["ip_addr"]) . '</td>';
    echo '<td style="border: 1px solid black; border-collapse: collapse;">' . htmlspecialchars($row["serv_name"]) . '</td>';
    echo '<td style="border: 1px solid black; border-collapse: collapse;">' . htmlspecialchars($row["status"]) . '</td>';
    echo '<td style="border: 1px solid black; border-collapse: collapse;">' . htmlspecialchars($row["lastchangedatetime"]) . '</td>';
    echo '</tr>';
}
$result->closeCursor();

echo "</tbody></table>";