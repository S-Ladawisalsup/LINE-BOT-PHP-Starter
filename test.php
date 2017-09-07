<?php

//header('Content-Type: text/html; charset=utf-8');

$array = file('greeting.txt');
$text = iconv( 'TIS-620' , 'UTF-8' ,$array);

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $text . '->' . $test . count($array);