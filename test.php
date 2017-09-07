<?php

$data = file('question.txt');

if (utf8_decode($data[0]) == '?') {
	echo "ok";
}
else {
	echo "not ok";
}

