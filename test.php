<?php

$data = file('question.txt');
$numindex = rand(0, (count($data) - 1));
$building = $data[$numindex];
echo $numindex;
echo $building;
$costr = strlen($building);
echo $costr;
echo substr($building,0,$costr - 3 );