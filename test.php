<?php

/*
note
Question format 7 types
1. "yes/no" question has index no. 1-5
2. "when" question (will answer as timing) has index no. 6-10
3. "where" qusetion (will answer as location) has index no. 11-12
4. "who" question (will answer as person) has index no. 13-15
5. "what/how" question (will answer as reason) has index no. 16-19
6. "which" question (will answer as object) has index no. 20-23 
7. "how+.." question (will answer as number) has index no. 24-27
*/

$init = 5;
$source = 0;

switch ($init) {
	case '0' ... '9':
		$source = 1;
		break;	
	default:
		$source = 2;
		break;
}

echo 'status : ' . $source;