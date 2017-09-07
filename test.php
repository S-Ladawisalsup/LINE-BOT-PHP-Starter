<?php

$data = file('question.txt');

if ($data[0] === '?') {
	$key = true;
}
else {
	$key = false;
}

echo $key;