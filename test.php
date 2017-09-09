<?php

$answer = file('text/answer.txt');
$ts = count($answer);
$building = $answer[2];
$building = substr($building, 0, strlen($building)-1);
echo $building;