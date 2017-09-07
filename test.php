<?php

$array = file('greeting.txt');
$array = iconv( 'UTF-8' , 'TIS-620' ,$array);

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $test . count($array);