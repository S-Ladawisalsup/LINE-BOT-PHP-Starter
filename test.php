<?php

$data = file('question.txt');
$numindex = rand(0, (count($data) - 1));
$building = $data[$numindex];
echo $building."\r\n";
echo count($building);
$costr = count($building)."\r\n";
echo substr($building,0,$costr - 3 )."\r\n";
echo substr($data[$numindex+1];,0,$costr - 3 )."\r\n";