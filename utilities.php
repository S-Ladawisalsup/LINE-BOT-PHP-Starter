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
			for ($i = 1; $i < 4; $i++) {
				array_push($question, $ismath[$i]);
			}
			break;
		case 'issqrt':
			$issqrt = file('text/math.txt');
			$question[] = null;
			for ($i = 1; $i < 4; $i++) {
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
	if ($packageId == 1) {
		$ra1 = rand(0, 2);
		if ($ra1 == 0) {
			$stickerId = rand(0, 18);
			if ($stickerId == 18) {
				$stickerId = 21;
			}
		}
		else if ($ra1 == 1) {
			$stickerId = rand(100, 139);
		}
		else if ($ra1 == 2) {
			$stickerId = rand(401, 430);
		}
	}
	else {
		$ra1 = rand(0, 2);
		if ($ra1 == 0) {
			$stickerId = rand(19, 47);
			if ($stickerId <= 21) {
				$stickerId = $stickerId - 1;
			}
		}
		else if ($ra1 == 1) {
			$stickerId = rand(140, 179);
		}
		else if ($ra1 == 2) {
			$stickerId = rand(501, 527);
		}
	}
	return array('packageId' => $packageId, 'stickerId' => $stickerId);
}
/**********************************************************************************************************************************/