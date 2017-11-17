<?php

date_default_timezone_set("Asia/Bangkok");
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
		case 'greeting':
			$question = file('text/greeting.txt');
			break;
		case 'math':
			//-------------------------------------------------
			//Old Version
			$ismath = file('text/question.txt');
			$question[] = null;
			for ($i = 28; $i <= 31; $i++) {
				array_push($question, $ismath[$i]);
			}		
			//-------------------------------------------------
			//New Version
			// $ismath = QuestionWordFromDB();
			// foreach ($ismath as $keyitem) {
			// 	if($keyitem['type'] == 7) {
			// 		if (endsWith($text, $keyitem['text']) {
			// 			return true;
			// 		}	
			// 	}
			// }
			//-------------------------------------------------		
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
1. "yes/no" question 
2. "when" question (will answer as timing) 
3. "where" qusetion (will answer as location)
4. "who" question (will answer as person)
5. "what/how" question (will answer as reason)
6. "which" question (will answer as object) 
7. "how+.." question (will answer as number)
Ohter(s) Mode!
8. It's ping to anther devices or server mode
*******************************************************************/

	if (is_ping_mode($text)) {
		return 8;
	}

	$QAArray = QuestionWordFromDB();
	foreach ($QAArray as $keyitems) {
		if (endsWith($text, $keyitems['text'])) {
			if ($keyitems['type'] == 1 && strpos($text, 'ล่ม') !== false) {
				return 8;
			}
			else {
				return $keyitems['type'];
			}
		}
	}
	return 0;
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
/**********************************************************************************************************************************/
function QuestionWordFromDB() {
	$dsn = 'pgsql:'
		. 'host=ec2-54-243-187-133.compute-1.amazonaws.com;'
		. 'dbname=dfusod038c3j35;'
		. 'user=mmbbbssobrmqjs;'
		. 'port=5432;'
		. 'sslmode=require;'
		. 'password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa';

	$db = new PDO($dsn);

	$query = 'SELECT id, questiontext, questiontype FROM tbhlinebotchkqa ORDER BY id ASC';
	$result = $db->query($query);

	$qwords = array();
	$index = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	    $qwords[$index] = array();
		$qwords[$index]['text'] = htmlspecialchars($row["questiontext"]);
		$qwords[$index]['type'] = htmlspecialchars($row["questiontype"]);
		$index = $index + 1;
	}
	$result->closeCursor();

	return $qwords;
}
/**********************************************************************************************************************************/
function TestWriteTempToDB() {

	$ttempt = 26;

	$db = pg_connect("host=ec2-54-243-187-133.compute-1.amazonaws.com 
					port=5432 
					dbname=dfusod038c3j35 
					user=mmbbbssobrmqjs 
					password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa");

	$result = pg_query($db, "UPDATE tbhlinebottemploc 
							SET temperature = $ttempt
							WHERE location = 'ITSD Room'");				
}
/**********************************************************************************************************************************/
function AddQText() {

	$ttempt = 26;

	$db = pg_connect("host=ec2-54-243-187-133.compute-1.amazonaws.com 
					port=5432 
					dbname=dfusod038c3j35 
					user=mmbbbssobrmqjs 
					password=fc2027eb6a706cd190646863367705a7969cbd85c0a86eed7a67d0dc6976bffa");

	$result = pg_query($db, "INSERT INTO tbhlinebotchkqa (questiontext, questiontype) VALUES 
		('มั้ย', '1')
		,('ไหม', '1')
		,('หรือไม่', '1')
		,('รึเปล่า', '1')
		,('หรือเปล่า', '1')
		,('รึยัง', '1')
		,('หรือยัง', '1')
		,('เมื่อไร', '2')
		,('เมื่อไหร่', '2')
		,('เมื่อใด', '2')
		,('เวลาไหน', '2')
		,('เวลาใด', '2')
		,('ที่ไหน', '3')
		,('ที่ใด', '3')
		,('ใคร', '4')
		,('คนไหน', '4')
		,('คนใด', '4')
		,('อะไร', '5')
		,('อย่างไร', '5')
		,('ยังไง', '5')
		,('ทำไม', '5')
		,('เหรอ', '5')
		,('หรอ', '5')
		,('สิ่งไหน', '6')
		,('สิ่งใด', '6')
		,('อันไหน', '6')
		,('อันใด', '6')
		,('เท่าไร', '7')
		,('เท่าไหร่', '7')
		,('เท่าใด', '7')
		;");				
}