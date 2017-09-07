<?php

$array = file('greeting.txt');

$array = utf8_encode($array);

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $test . count($array);