<?php

/*******************************************************************
NOTE!
Question has format 7 types!
1. "yes/no" question has index no. 1-5
2. "when" question (will answer as timing) has index no. 6-10
3. "where" qusetion (will answer as location) has index no. 11-12
4. "who" question (will answer as person) has index no. 13-15
5. "what/how" question (will answer as reason) has index no. 16-19
6. "which" question (will answer as object) has index no. 20-23 
7. "how+.." question (will answer as number) has index no. 24-27
*******************************************************************/

$init = 79;
$source = 0;

switch ($init) {
	case $init > 80:
		$source = A;
		break;
	case $init > 50:
		$source = B;
		break;	
	default:
		$source = F;
		break;
}

echo 'status : ' . $source;