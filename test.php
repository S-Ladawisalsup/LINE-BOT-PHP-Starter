<?php

$data = file('question.txt');
$numindex = rand(0, (count($data) - 1));
$building = $data[$numindex];
echo $building."\n";
echo count($building)."\r\n";
$costr = count($building);
echo substr($building,0,$costr - 2 );