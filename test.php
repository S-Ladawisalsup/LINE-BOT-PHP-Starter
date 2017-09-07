<?php

$data = file('question.txt');

foreach ($data as $item) {
	echo $item . '<br>';
}

echo count($data);