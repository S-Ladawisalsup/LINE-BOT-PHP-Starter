<?php

$array = file('greeting.txt');

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $test . count($array);