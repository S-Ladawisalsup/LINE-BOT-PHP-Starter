<?php

header('Content-Type: text/html; charset=utf-8');

$array = file('greeting.txt');

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $test . count($array);