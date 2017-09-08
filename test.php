<?php

$data = file('question.txt');
$numindex = rand(0, (count($data) - 1));
$building = $answer[$numindex];
echo count($building);
echo $building;