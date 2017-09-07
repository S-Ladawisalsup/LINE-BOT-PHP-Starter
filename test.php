<?php

$data = file('question.txt');

$key = 'false';

if ($data[0] == utf8_encode('?')) {
	$key = 'true';
}

echo $data[0].$key;