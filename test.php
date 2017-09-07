<?php

$array = file('greeting.txt');

$array = mb_convert_encoding($array, 'utf8_encode');

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $test . count($array);