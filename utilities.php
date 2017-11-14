<?php
/**********************************************************************************************************************************/
/*** Function for check word(s) contain(s) start or end at string. ***/
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}
/**********************************************************************************************************************************/
/*** Function for calculate basic mathematics when string contain math's operator. ***/
function maths($a, $b, $operator) {
    switch ($operator) {
    	case '+':
    	case 'บวก':
    		return $a + $b;
    	case '-':
    	case 'ลบ':
    		return $a - $b;
    	case '*':
    	case 'คูณ':
    	case 'x':
    	case 'X':
    	case '×':
    		return $a * $b;
    	case '/':
    	case 'หาร':
    		if ($b != 0) {
    			return $a / $b;
    		}
    		else {
    			return 'ตัวหารเป็น 0 ไม่ไก้ ไปคิดมาใหม่นะ';
    		}
    	case '%':
    		return $a % $b;
    	case 'ยกกำลัง':
    	case 'pow':
    		return pow($a, $b);
    	case 'รูทที่สอง':
    	case 'รากที่สอง':
    	case 'sqrt':
    		return sqrt($a); 	
    	default:
    		return 'โอ๊ยปวดหัว คิดไม่ออกแล้ว';
    }
}
/**********************************************************************************************************************************/
/*** Function for check text from user is question(?) ***/
function GetQuesion($text, $flag) {
	switch ($flag) {
		case 'quiz':
			$question = file('text/question.txt');
			break;
		case 'greeting':
			$question = file('text/greeting.txt');
			break;
		case 'math':
			$ismath = file('text/question.txt');
			$question[] = null;
			for ($i = 26; $i <= 29; $i++) {
				array_push($question, $ismath[$i]);
			}
			break;
		case 'issqrt':
			$issqrt = file('text/math.txt');
			$question[] = null;
			for ($i = 1; $i <= 3; $i++) {
				array_push($question, $issqrt[$i]);
			}
			break;
		default:
			return false;
	}	

	foreach ($question as $item) {
		$item = substr($item, 0, strlen($item) - 1);
		if (endsWith($text, $item)) {
			return true;
		}
	}

	return false;
}
/**********************************************************************************************************************************/
/*** Function generates answer as text type, now get answer from array text file by random (cannot connect datatabase now) ***/
function AnswerBuilder($mood) {
	switch ($mood) {
		case 'ans':
			$answer = file('text/answer.txt');
			break;		
		default:
			$answer = file('text/reply.txt');
			break;
	}

	$building = 'error';
	if (count($answer) > 0) {
		$numindex = rand(1, count($answer));
		$building = $answer[$numindex];
		$building = substr($building, 0, strlen($building)-1);
	}
	return $building;
}
/**********************************************************************************************************************************/
/*** Function generates answer as sticker type by random from default sticker(s) by LINE Corp. ***/
function GetSticker() {
	$packageId = rand(1, 2);
	$randType = rand(0, 2);

	if ($packageId == 1) {		
		if ($randType == 0) {
			$stickerId = rand(0, 18);
			if ($stickerId == 18) {
				$stickerId = 21;
			}
		}
		else if ($randType == 1) {
			$stickerId = rand(100, 139);
		}
		else {
			$stickerId = rand(401, 430);
		}
	}
	else {
		if ($randType == 0) {
			$stickerId = rand(19, 47);
			if ($stickerId <= 21) {
				$stickerId = $stickerId - 1;
			}
		}
		else if ($randType == 1) {
			$stickerId = rand(140, 179);
		}
		else {
			$stickerId = rand(501, 527);
		}
	}
	return array('packageId' => $packageId, 'stickerId' => $stickerId);
}
/**********************************************************************************************************************************/
function findQuestionType ($text) {

/*******************************************************************
NOTE!
Question has 7 formats!
1. "yes/no" question has index no. 1-5
2. "when" question (will answer as timing) has index no. 6-10
3. "where" qusetion (will answer as location) has index no. 11-12
4. "who" question (will answer as person) has index no. 13-15
5. "what/how" question (will answer as reason) has index no. 16-21
6. "which" question (will answer as object) has index no. 22-25 
7. "how+.." question (will answer as number) has index no. 26-29
Ohter(s) Mode!
8. It's ping to anther devices or server mode
*******************************************************************/

	if (is_ping_mode($text)) {
		return 8;
	}

	$QArray = file('text/question.txt');
	$counter = 0;
	foreach ($QArray as $keyitem) {
		$keyitem = substr($keyitem, 0, strlen($keyitem) - 1);
		if (endsWith($text, $keyitem)) {
			break;
		}	
		$counter = $counter + 1;
	}

	switch ($counter) {
		case '0':
			return 0;
		case $counter <= 5:
			if (strpos($text, 'ล่ม') !== false) {
				return 8;
			}
			return 1;
		case $counter <= 10:
			return 2;
		case $counter <= 12:
			return 3;
		case $counter <= 15:
			return 4;
		case $counter <= 21:
			return 5;
		case $counter <= 25:
			return 6;
		case $counter <= 29:
			return 7;
		default:
			return 0;
	}
}
/**********************************************************************************************************************************/
function is_ping_mode ($text) {
	$pingping = file('text/ping.txt');
	foreach ($pingping as $pingword) {
		$pingword = substr($pingword, 0, strlen($pingword) - 1);
		if (strpos($text, $pingword) !== false) {
			return true;
		}
	}
	return false;
}