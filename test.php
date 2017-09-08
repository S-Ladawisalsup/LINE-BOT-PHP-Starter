<?php

$data = file('question.txt');
$numindex = rand(0, (count($data) - 1));
$building = $data[14];
echo $building;
$costr = strlen($building);
echo $costr;
echo substr($building,0,$costr - 1 );
echo $costr-1;