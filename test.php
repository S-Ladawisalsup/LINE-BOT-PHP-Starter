<?php

$array = file('greeting.txt');
$array = iconv_substr($array, 0,100, "UTF-8");

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $test . count($array);