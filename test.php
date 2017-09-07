<?php

$array = file('greeting.txt');

$array = utf8_decode($array);

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $test . count($array);