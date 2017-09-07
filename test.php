<?php

$data = file('greeting.txt');

foreach ($data as $item) {
	echo $item . ' / ';
}

echo count($data);