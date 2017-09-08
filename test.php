<?php

$data = file('question.txt');
$numindex = rand(0, (count($data) - 1));
$building = $data[$numindex];
echo $building."\n";
$costr = strlen($building);
echo $costr."\r\n";
echo substr($building,0,$costr - 2 );