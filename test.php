<?php

$data = file('question.txt');

foreach ($data as $key) {
	echo utf8_decode($key);
}