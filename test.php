<?php

echo date('l').'Fini';
$t= time('GMT+7');
echo $t . "<br>";
echo(date("Y-m-d||H:i:sP",$t) . "<br>");
$day = strtolower(substr(date('UTC+7'), 0, 3));
$da=date('Thailand/Bangkok');
echo "da = " .$da."<br>";
echo "day = " . $day;