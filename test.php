<?php

echo date('UTC+7').'Fini'. "<br>";
date_default_timezone_set("Asia/Bangkok");
$t= time();
echo $t . "<br>";
echo(date("Y-m-d||H:i:sP",$t) . "<br>");
$day = strtolower(substr(date(), 0, 3));
$da=date();
echo "da = " .$da."<br>";
echo "day = " . $day;