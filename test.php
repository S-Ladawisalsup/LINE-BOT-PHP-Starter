<?php

$data = file('question.txt');
$numindex = rand(0, (count($data) - 1));
$building = $data[$numindex];
echo $building;
echo '\r\n';
echo count($building);
$costr = count($building);
echo '\r\n';
echo substr($building,0,$costr - 4 );