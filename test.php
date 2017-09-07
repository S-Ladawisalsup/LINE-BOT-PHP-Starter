<?php

$array = file('greeting.txt');
$text = iconv('TIS-620','UTF-8//ignore',$array);

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $text . '=>' . $test . count($array);