<?php

$array = file('greeting.txt');
$text = iconv(mb_detect_encoding($array, mb_detect_order(), true), "UTF-8", $array);

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $text . '=>' . $test . count($array);