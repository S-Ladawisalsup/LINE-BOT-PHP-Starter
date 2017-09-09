<?php

$answer = file('text/question.txt');
$ts = count($answer);
$building = $answer[1];
$building = substr($building, 0, strlen($building)-1);
echo $building;