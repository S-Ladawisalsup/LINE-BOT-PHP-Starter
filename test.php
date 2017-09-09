<?php

$answer = file('text/question.txt');
$ts = count($answer);
$building = $answer[0];
$building = substr($building, 0, strlen($building)-1);
echo $building.'<br>';
$numindex = rand(0,
	8);
echo $numindex;