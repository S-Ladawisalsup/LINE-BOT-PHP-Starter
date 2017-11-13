<?php

$TInput = '?';
$TArray = file('text/question.txt');
$Counters = 0;

foreach ($vtest as $TArray) {
	$vtest = substr($vtest, 0, strlen($vtest) - 1);
	if ($TInput == $vtest) {
		break;
	}	
	$Counters = $Counters + 1;
}

$ShowType = 'zero';
switch ($Counters) {
	case '0':
		$ShowType = '0. none';
		break;
	case $counter <= 5:
		$ShowType = '1. yes/no question';
		break;
	case $counter <= 10:
		$ShowType = '2. when question';
		break;
	case $counter <= 12:
		$ShowType = '3. where question';
		break;
	case $counter <= 15:
		$ShowType = '4. who question';
		break;
	case $counter <= 21:
		$ShowType = '5. what/how question';
		break;
	case $counter <= 25:
		$ShowType = '6. which question';
		break;
	case $counter <= 29:
		$ShowType = '7. number question';
		break;
	default:
		$ShowType = 'default';
		break;
}

echo 'counter : ' . $Counters . ',';