<?php

$data = file('question.txt');

$key = 'false';

if ($data[0] === '?') {
	$key = 'true';
}

echo $key;