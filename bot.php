<?php

/************************************************************************************************************************************/
/*** PHP Function Zone. ***/
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

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

function GetQuesion($text) {
	$question = [
		0 => '?',
		1 => 'อะไร',
		2 => 'ที่ไหน',
		3 => 'ที่ใด',
		4 => 'ใคร',
		5 => 'เมื่อไร',
		6 => 'เมื่อไหร่',
		7 => 'เมื่อใด',
		8 => 'เวลาไหน',
		9 => 'เวลาใด',
		10 => 'อย่างไร',
		11 => 'ยังไง',
		12 => 'เท่าไร',
		13 => 'เท่าไหร่',
		14 => 'หรือไม่',
		15 => 'ไหม',
		16 => 'มั้ย',
		17 => 'ทำไม',
		18 => 'สิ่งไหน',
		19 => 'สิ่งใด',
		20 => 'อันไหน',
		21 => 'อันใด',
		22 => 'เท่าใด',
	];

	foreach ($question as $item) {
		if (endsWith($text, $item)) {
			return true;
		}
	}

	return false;
}

function AnswerBuilder($mood) {

	switch ($mood) {
		case 'ans':
			$answer = file('answer.txt');
			break;		
		default:
			$answer = file('reply.txt');
			break;
	}

	$building = 'error';
	if (count($answer) > 0) {
		$numindex = rand(0, (count($answer) - 1));
		$building = $answer[$numindex];
// oa edit
		$costr = strlen($building);
		$subs = substr($building,0,$costr - 1 );
//
	}
	return $subs; // oa edit
//	return $building;
}
/************************************************************************************************************************************/


$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

$bot_name = '@kiki';

$question = [
	0 => '?',
	1 => 'ไหม',
	2 => 'มั้ย',
	3 => 'หรือไม่',
	4 => 'เท่าไร',
	5 => 'เท่าไหร่',
	6 => 'อะไร',
];

$mathematics = [
	0 => '+',
	1 => '-',
	2 => 'x',
	3 => '*',
	4 => '×',
	5 => '÷',
	6 => '/',
	7 => '%',
	8 => 'บวก',
	9 => 'ลบ',
	10 => 'คูณ',
	11 => 'หาร',
	12 => 'X',
	13 => 'ยกกำลัง',
	14 => 'pow',
	15 => 'รากที่สอง',
	16 => 'รูทที่สอง',
	17 => 'sqrt',
];

// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message') {

			if ($event['message']['type'] == 'text') {

				// Compare message calling bot's name
				$haystack = strtolower($event['message']['text']);
				if (startsWith($haystack, $bot_name)) {

					// Get text sent echo without bot's name
					$text = substr($event['message']['text'], strlen($bot_name));

					if (GetQuesion($text)) {
						if (endsWith($text, $question[0]) || endsWith($text, $question[4]) || endsWith($text, $question[5]) ) {
							foreach ($mathematics as $math) {
								if (strpos($text, $math)) {
									$operator = $math;
									break;
								}
								else {
									$operator = 'null';
								}
							}

							if ($operator != 'null') {
								preg_match_all('!\d+\.*\d*!', $text, $matches);
								$val = $matches[0];

								if ((count($val) == 1) && ($operator == $mathematics[15] || $operator == $mathematics[16] || $operator == $mathematics[17])) {
									$solve = maths($val[0], 0, $operator);
								}
								else if (count($val) == 2) {
									$solve = maths($val[0], $val[1], $operator);
								}							 
							}

							if (isset($solve) === false) {
								$messages = [						
									'type' => 'text',
									'text' => AnswerBuilder('ans')
								];	
							}
							else {
								$messages = [						
									'type' => 'text',
									'text' => $solve . " จ้า"
								];	
							}
						}
						else {
							$messages = [						
								'type' => 'text',
								'text' => AnswerBuilder('ans')
							];							
						}
					}

					/* test token */
					else if (strpos($text, 'token') !== false) {
						$token = $event['replyToken'];
						$messages = [
							'type' => 'text',
							'text' => $token,
						];	
					}

					// Check text is greeting
					else if ((strpos($text, 'สวัสดี') !== false) || (strpos($text, 'หวัดดี') !== false) || (strpos($text, 'ดีจ้า') !== false) || (strpos($text, 'อรุณสวัสดิ์') !== false)) {
						$day = strtolower(substr(date('l'), 0, 3));

						$messages = [
							'type' => 'image',
						    'originalContentUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/' . $day . '_original.jpg',
						    'previewImageUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/' . $day . '_240.jpg'
						];
					}
					else {
						// Build message to reply back
						$messages = [						
							'type' => 'text',
							'text' => AnswerBuilder('res')
						];	
					}
				}			
			}
			else if ($event['message']['type'] == 'sticker') {
				// Get random number of sticker
				$var = rand(1,20);

				// Build message to reply back
				$messages = [
					'type' => 'sticker',
					'packageId' => '1',
    				'stickerId' => $var
				];
			}

			// Get replyToken
			$replyToken = $event['replyToken'];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);

			echo $result . "\r\n";	
		}
	}
}
echo "OK";