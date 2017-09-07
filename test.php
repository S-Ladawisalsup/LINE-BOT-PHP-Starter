<?php

$array = file('greeting.txt');
$text = iconv('UTF-8','TIS-620//ignore',$array);

foreach ($array as $item) {
	$test .= $item . '/';
}

echo $text . '=>' . $test . count($array);