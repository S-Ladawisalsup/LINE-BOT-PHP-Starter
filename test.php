<?php

$data = file('question.txt');

foreach ($data as $key) {
	$decodetalker = utf8_decode($key);
	$encodetalker = utf8_encode($decodetalker);
	echo $key . '/' . $decodetalker . '/' . $encodetalker . '<br>';
}