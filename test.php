<?php

//header('Content-Type: text/html; charset=utf-8');

$array = file('greeting.txt');
$text =  iconv_substr($array, 0, 100, "UTF-8");

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $text . $test . count($array);