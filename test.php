<?php

$data = file('question.txt');

foreach ($data as $key) {
	echo $key;
}