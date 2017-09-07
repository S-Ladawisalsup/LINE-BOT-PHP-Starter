<?php

$data = file('greeting.txt');

$data = mb_convert_encoding($data, 'HTML-ENTITIES', "UTF-8");

foreach ($data as $item) {
	echo $item . '<br>';
}