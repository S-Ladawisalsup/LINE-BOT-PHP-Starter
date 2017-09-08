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
/************************************************************************************************************************************/


$access_token = 'CFecc4UnPdpCUxVk2VuTlf7ANCYHbCpaxYltjR/z15zMJ/KzsPIVrp4tCql4xmQYr8qgJSZ6oitEZ0/PKH+FpdneucSfPgjTP03mQ5KRSKqYT93fEEvGDqOUxJ/SBoS3oTXcJaRSxlPVBWxH+8PWxAdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

$bot_name = '@kiki';

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

					if (GetQuesion($text, 'quiz')) {
						if (GetQuesion($text, 'math')) {
							$mathematics = file('text/math.txt');
							foreach ($mathematics as $math) {
								$math = substr($math, 0, strlen($math) - 1);
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

								if ((count($val) == 1) && GetQuesion($operator, 'issqrt')) {
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
					// Check text is greeting
					else if (GetQuesion($text, 'greeting')) {
						$day = strtolower(substr(date('l'), 0, 3));

						$messages = [
							'type' => 'image',
						    'originalContentUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/' . $day . '_original.jpg',
						    'previewImageUrl' => 'https://cryptic-harbor-32168.herokuapp.com/images/' . $day . '_240.jpg'
						];
					}
					else if (strpos($text, 'Who am I')) {
						$messages = [						
							'type' => 'text',
							'text' => 'สวัสดี ID คุณคือ ' . $event['source']['userId']
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

$touser = 'Ua492767fd96449cd8a857b101dbdbcce';

$messages = [
	'type' => 'text',
	'text' => AnswerBuilder('res')
];

// Make a POST Request to Messaging API to push to sender
$url = 'https://api.line.me/v2/bot/message/push';
$data = [
	'to' => $touser,
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

echo "OK";