<?php

header('Content-Type: text/html; charset=utf-8');

$array = file('greeting.txt');
$text = iconv( 'UTF-8' , 'TIS-620' ,$text);

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $text . '=>' . $test . count($array);