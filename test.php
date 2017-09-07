<?php

$data = file('question.txt');

$data = str_replace('  ', '', $data);

foreach ($data as $item) {
	echo $item . '<br>';
}

echo count($data);